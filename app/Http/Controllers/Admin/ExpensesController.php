<?php

namespace App\Http\Controllers\Admin;

use App\PaymentValue;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePaymentValueRequest;
use App\Http\Requests\UpdatePaymentValueRequest;
use App\Http\Resources\Admin\AdminPaymentValueIndexResource;
use App\Http\Resources\Base\BasePaymentItemIndexResource;
use App\Http\Resources\Base\BasePaymentValueIndexResource;
use App\PaymentItem;
use App\Services\Admin\AdminPaymentValueService;
use App\Services\Base\BasePaymentValueService;
use App\Specialist;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpensesController extends Controller
{
    public $basePaymentValueService;

    public function __construct(BasePaymentValueService $basePaymentValueService)
    {
        $this->basePaymentValueService = $basePaymentValueService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $payment_items = BasePaymentItemIndexResource::collection(PaymentItem::all());
        return view('backend.pages.expenses.today', compact('payment_items'));
    }
    public function getJson(Request $request)
    {
        return BasePaymentValueIndexResource::collection(
            $this->basePaymentValueService->fetchRecords()->paginate()
        );
    }
    public function getJsonToday(Request $request)
    {
        return BasePaymentValueIndexResource::collection(
            $this->basePaymentValueService->fetchRecords()->whereDate('created_at', Carbon::today())->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreatePaymentValueRequest $request)
    {
        $payment_value =  $this->basePaymentValueService->create($request);
        return response()->json([
            'data' => $payment_value
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentValue $expense)
    {
        $payment_value = $expense;
        $payment_items = BasePaymentItemIndexResource::collection(PaymentItem::all());
        return view('backend.pages.expenses.edit', compact('payment_value', 'payment_items'));
    }
    public function show(PaymentValue $expense)
    {
        return redirect('/expenses');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePaymentValueRequest $request, PaymentValue $expense)
    {
        $payment_value =  $this->basePaymentValueService->update($request, $expense);
        return response()->json([
            'data' => $payment_value
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
