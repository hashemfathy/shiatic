<?php

namespace App\Http\Controllers\Admin;

use App\PaymentItem;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreatePaymentItemRequest;
use App\Http\Requests\UpdatePaymentItemRequest;
use App\Http\Resources\Admin\AdminPaymentItemIndexResource;
use App\Http\Resources\Base\BasePaymentItemIndexResource;
use App\Services\Admin\AdminPaymentItemService;
use App\Services\Base\BasePaymentItemService;
use App\Specialist;
use Illuminate\Http\Request;

class PaymentItemController extends Controller
{
    public $basePaymentItemService;

    public function __construct(BasePaymentItemService $basePaymentItemService)
    {
        $this->basePaymentItemService = $basePaymentItemService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.pages.finance.add_item');
    }
    public function getJson(Request $request)
    {
        return BasePaymentItemIndexResource::collection(
            $this->basePaymentItemService->fetchRecords()->paginate()
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.pages.finance.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreatePaymentItemRequest $request)
    {
        $payment_item =  $this->basePaymentItemService->create($request);
        return response()->json([
            'data' => $payment_item
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(PaymentItem $payment_item)
    {
        return view('backend.pages.finance.edit', compact('payment_item'));
    }
    public function show(PaymentItem $payment_item)
    {
        return redirect('/payment-items');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatePaymentItemRequest $request, PaymentItem $payment_item)
    {
        $payment_item =  $this->basePaymentItemService->update($request, $payment_item);
        return response()->json([
            'data' => $payment_item
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
