<?php

namespace App\Http\Controllers;

use App\Models\Request as BookingRequest;
use App\Services\KashierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class KashierPaymentController extends Controller
{
    protected KashierService $kashierService;

    public function __construct(KashierService $kashierService)
    {
        $this->kashierService = $kashierService;
    }

    /**
     * Initiate payment for a booking
     */
    public function pay($id)
    {
        $booking = BookingRequest::findOrFail($id);

        if ($booking->payment_status === 'paid') {
            return redirect()->route('payment.kashier.success', ['id' => $booking->id]);
        }

        // Calculate total deposit
        $totalDeposit = (float) $booking->deposit;
        if ($booking->children()->exists()) {
            $totalDeposit += (float) $booking->children()->sum('deposit');
        }

        // If deposit is 0 for some reason, fallback to total_price or minimum
        if ($totalDeposit <= 0) {
            $totalDeposit = (float) ($booking->total_price ?: 100);
        }

        // The booking deposit is already grossed up with Kashier commission during booking creation,
        // so the charged amount directly equals $totalDeposit.
        $amountToCharge = $totalDeposit;

        $orderId = 'SHIATIC-' . $booking->id . '-' . time();

        $sessionResult = $this->kashierService->createPaymentSession([
            'order_id' => $orderId,
            'amount' => $amountToCharge,
            'currency' => 'EGP',
            'redirect_url' => route('payment.kashier.callback'),
            'customer' => [
                'reference' => (string) $booking->id,
                'name' => $booking->name,
                'phone' => $booking->phone,
                'email' => 'booking@shiatic.com',
            ],
        ]);

        if ($sessionResult['success'] && !empty($sessionResult['session_url'])) {
            $booking->update([
                'payment_method' => 'kashier',
                'payment_meta' => json_encode(['order_id' => $orderId, 'session_id' => $sessionResult['session_id'] ?? null]),
            ]);

            return redirect()->away($sessionResult['session_url']);
        }

        Log::error('Kashier initiation error for booking #' . $booking->id, ['result' => $sessionResult]);

        return view('payment.failed', [
            'booking' => $booking,
            'errorMessage' => $sessionResult['message'] ?? 'حدث خطأ أثناء الاتصال ببوابة كاشير، يرجى المحاولة مرة أخرى.',
        ]);
    }

    /**
     * Handle Kashier return redirect
     */
    public function callback(Request $request)
    {
        $all = $request->all();
        Log::info('Kashier callback received: ', $all);

        $order = $request->input('order') ?? $request->input('merchantOrderId') ?? $request->input('orderId');
        $paymentStatus = strtoupper((string) ($request->input('paymentStatus') ?? $request->input('status') ?? ''));

        $bookingId = null;
        if ($order && preg_match('/SHIATIC-(\d+)/', $order, $matches)) {
            $bookingId = (int) $matches[1];
        }

        if (!$bookingId && $request->has('reference')) {
            $bookingId = (int) $request->input('reference');
        }

        $booking = $bookingId ? BookingRequest::find($bookingId) : null;

        if (!$booking) {
            Log::error('Kashier callback: booking not found for order ' . ($order ?: 'unknown'));
            return view('payment.failed', [
                'booking' => null,
                'errorMessage' => 'لم نتمكن من العثور على الحجز المقترن برقم العملية.',
            ]);
        }

        $signatureValid = $this->kashierService->verifySignature($all);

        // In test mode or when debug enabled, log signature status
        if (!$signatureValid && !config('app.debug')) {
            Log::warning('Kashier callback signature failed for booking #' . $booking->id);
            return view('payment.failed', [
                'booking' => $booking,
                'errorMessage' => 'فشل التحقق من التوقيع الرقمي للعملية (Invalid Signature).',
            ]);
        }

        $transactionId = $request->input('transactionId')
            ?? $request->input('kashierTxnId')
            ?? $request->input('cardToken')
            ?? $order;

        $amountPaid = (float) ($request->input('amount') ?? $booking->deposit);

        if ($paymentStatus === 'SUCCESS') {
            $booking->update([
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'payment_method' => 'kashier',
                'transaction_id' => $transactionId,
                'paid_amount' => $amountPaid,
                'paid_at' => now(),
                'payment_meta' => json_encode($all),
            ]);

            // Update group members if any
            if ($booking->children()->exists()) {
                $booking->children()->update([
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'payment_method' => 'kashier',
                    'transaction_id' => $transactionId,
                    'paid_at' => now(),
                ]);
            }

            // Send Email notification to clinic
            try {
                $recipient = config('mail.to_address') ?? config('mail.from.address') ?? 'ninjahunted26@gmail.com';
                if ($recipient) {
                    $booking->load('children');
                    $isGroup = $booking->children && $booking->children->count() > 0;
                    $subject = ($isGroup ? '✅ تم سداد حجز جماعي أونلاين - ' : '✅ تم سداد حجز أونلاين - ') . $booking->name . " (كاشير: {$transactionId})";
                    Mail::to($recipient)->send(new \App\Mail\NewRequestMail($booking, $subject));
                }
            } catch (\Exception $e) {
                Log::error('Failed to send payment email notification: ' . $e->getMessage());
            }

            return view('payment.success', [
                'booking' => $booking,
                'transactionId' => $transactionId,
                'amountPaid' => $amountPaid,
                'data' => $all,
            ]);
        }

        // Payment not successful
        $booking->update([
            'payment_status' => 'failed',
            'payment_meta' => json_encode($all),
        ]);

        return view('payment.failed', [
            'booking' => $booking,
            'errorMessage' => 'لم تكتمل عملية الدفع أو تم إلغاؤها من قبل المستخدم.',
        ]);
    }

    /**
     * Handle Kashier Webhook (Server-to-Server)
     */
    public function webhook(Request $request)
    {
        $all = $request->all();
        Log::info('Kashier webhook received: ', $all);

        $order = $request->input('order') ?? $request->input('merchantOrderId') ?? $request->input('orderId');
        $paymentStatus = strtoupper((string) ($request->input('paymentStatus') ?? $request->input('status') ?? ''));

        $bookingId = null;
        if ($order && preg_match('/SHIATIC-(\d+)/', $order, $matches)) {
            $bookingId = (int) $matches[1];
        }

        $booking = $bookingId ? BookingRequest::find($bookingId) : null;

        if ($booking && $paymentStatus === 'SUCCESS' && $booking->payment_status !== 'paid') {
            $transactionId = $request->input('transactionId') ?? $request->input('kashierTxnId') ?? $order;
            $amountPaid = (float) ($request->input('amount') ?? $booking->deposit);

            $booking->update([
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'payment_method' => 'kashier',
                'transaction_id' => $transactionId,
                'paid_amount' => $amountPaid,
                'paid_at' => now(),
                'payment_meta' => json_encode($all),
            ]);

            if ($booking->children()->exists()) {
                $booking->children()->update([
                    'status' => 'confirmed',
                    'payment_status' => 'paid',
                    'payment_method' => 'kashier',
                    'transaction_id' => $transactionId,
                    'paid_at' => now(),
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Success receipt view
     */
    public function successReceipt($id)
    {
        $booking = BookingRequest::findOrFail($id);
        return view('payment.success', [
            'booking' => $booking,
            'transactionId' => $booking->transaction_id ?: ('TXN-' . $booking->id),
            'amountPaid' => $booking->paid_amount ?: $booking->deposit,
            'data' => [],
        ]);
    }
}
