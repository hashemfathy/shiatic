<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class KashierService
{
    public function getMid(): string
    {
        return Setting::get('kashier_mid') ?: config('kashier.mid', 'MID-48236-645');
    }

    public function getApiKey(): string
    {
        return Setting::get('kashier_api_key') ?: config('kashier.api_key', '');
    }

    public function getSecretKey(): string
    {
        return Setting::get('kashier_secret_key') ?: config('kashier.secret_key', '');
    }

    public function getMode(): string
    {
        return Setting::get('kashier_mode') ?: config('kashier.mode', 'test');
    }

    public function getBaseUrl(): string
    {
        $mode = $this->getMode();
        $endpoints = config('kashier.endpoints', []);
        return $endpoints[$mode] ?? ($mode === 'live' ? 'https://api.kashier.io' : 'https://test-api.kashier.io');
    }

    public function getCommissionPercent(): float
    {
        $val = Setting::get('kashier_commission_percent');
        return $val !== null ? (float) $val : (float) config('kashier.commission_percent', 2.75);
    }

    public function getCommissionFixed(): float
    {
        $val = Setting::get('kashier_commission_fixed');
        return $val !== null ? (float) $val : (float) config('kashier.commission_fixed', 3.00);
    }

    /**
     * Calculate the gross amount to charge on Kashier
     * Formula: Gross = (Net + FixedFee) / (1 - (Percent / 100))
     * Example: for 720 EGP net, with 2.75% + 3 EGP:
     * (720 + 3) / (1 - 0.0275) = 743.44 EGP
     * When Kashier deducts 2.75% + 3 EGP (23.44 EGP), the clinic receives exactly 720.00 EGP net!
     */
    public function calculateGrossAmount(float $netAmount): float
    {
        if ($netAmount <= 0) {
            return 0.0;
        }

        $percent = $this->getCommissionPercent();
        $fixed = $this->getCommissionFixed();

        if ($percent <= 0 && $fixed <= 0) {
            return (float) ceil($netAmount);
        }

        $rate = $percent / 100.0;
        if ($rate >= 1.0) {
            return (float) ceil($netAmount + $fixed);
        }

        $gross = ($netAmount + $fixed) / (1.0 - $rate);
        return (float) ceil($gross);
    }

    /**
     * Create a payment session in Kashier v3
     */
    public function createPaymentSession(array $orderData): array
    {
        $endpoint = rtrim($this->getBaseUrl(), '/') . '/v3/payment/sessions';
        $mid = $this->getMid();
        $apiKey = $this->getApiKey();
        $secretKey = $this->getSecretKey();

        $payload = [
            'amount' => (string) number_format((float) $orderData['amount'], 2, '.', ''),
            'currency' => $orderData['currency'] ?? config('kashier.currency', 'EGP'),
            'order' => (string) $orderData['order_id'],
            'merchantId' => $mid,
            'merchantRedirect' => $orderData['redirect_url'] ?? route('payment.kashier.callback'),
            'type' => 'one-time',
            'allowedMethods' => 'card,wallet',
            'customer' => [
                'reference' => (string) ($orderData['customer']['reference'] ?? 'cust-' . time()),
                'name' => (string) ($orderData['customer']['name'] ?? 'Client'),
                'email' => (string) ($orderData['customer']['email'] ?? 'booking@shiatic.com'),
                'phone' => (string) ($orderData['customer']['phone'] ?? '01000000000'),
            ],
        ];

        Log::info('Kashier creating payment session: ', ['endpoint' => $endpoint, 'order' => $orderData['order_id'], 'amount' => $payload['amount']]);

        $response = Http::withHeaders([
            'Authorization' => $secretKey,
            'api-key' => $apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post($endpoint, $payload);

        if ($response->successful()) {
            $data = $response->json();
            Log::info('Kashier session created successfully: ', ['sessionId' => $data['_id'] ?? null, 'sessionUrl' => $data['sessionUrl'] ?? null]);
            return [
                'success' => true,
                'session_url' => $data['sessionUrl'] ?? null,
                'session_id' => $data['_id'] ?? null,
                'data' => $data,
            ];
        }

        $errorBody = $response->body();
        Log::error('Kashier session creation failed: ' . $errorBody);

        return [
            'success' => false,
            'message' => 'تعذر الاتصال ببوابة الدفع كاشير: ' . ($response->json('message') ?? $errorBody),
            'raw' => $errorBody,
        ];
    }

    /**
     * Verify incoming HMAC-SHA256 signature from Kashier
     */
    public function verifySignature(array $params): bool
    {
        if (empty($params['signature'])) {
            return false;
        }

        $receivedSignature = (string) $params['signature'];
        unset($params['signature']);

        // Remove parameters that might be added by browser/laravel routing
        unset($params['mode']);

        uksort($params, 'strcmp');

        // Build standard query string
        $queryString = [];
        foreach ($params as $key => $value) {
            if (is_array($value)) continue;
            $queryString[] = $key . '=' . $value;
        }
        $payloadRaw = implode('&', $queryString);

        $apiKey = $this->getApiKey();
        $secretKey = $this->getSecretKey();

        // Check with API key
        $calcWithApiKey = hash_hmac('sha256', $payloadRaw, $apiKey);
        if (hash_equals($calcWithApiKey, $receivedSignature)) {
            return true;
        }

        // Check with urlencoded values
        $queryStringEncoded = [];
        foreach ($params as $key => $value) {
            if (is_array($value)) continue;
            $queryStringEncoded[] = $key . '=' . urlencode((string)$value);
        }
        $payloadEncoded = implode('&', $queryStringEncoded);
        $calcEncodedWithApiKey = hash_hmac('sha256', $payloadEncoded, $apiKey);
        if (hash_equals($calcEncodedWithApiKey, $receivedSignature)) {
            return true;
        }

        // Also check with secret key if different
        if ($secretKey) {
            $calcWithSecret = hash_hmac('sha256', $payloadRaw, $secretKey);
            if (hash_equals($calcWithSecret, $receivedSignature)) {
                return true;
            }
            $calcEncodedWithSecret = hash_hmac('sha256', $payloadEncoded, $secretKey);
            if (hash_equals($calcEncodedWithSecret, $receivedSignature)) {
                return true;
            }
        }

        Log::warning('Kashier signature mismatch', [
            'received' => $receivedSignature,
            'calc_api_key' => $calcWithApiKey,
            'payload_sample' => $payloadRaw,
        ]);

        return false;
    }
}
