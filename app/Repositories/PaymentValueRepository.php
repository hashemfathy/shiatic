<?php

namespace App\Repositories;

use App\PaymentValue;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;


class PaymentValueRepository
{
    public $model;

    public function __construct(PaymentValue $model)
    {
        $this->model = $model;
    }

    public function create(Request $request)
    {
        $payment_value = new PaymentValue();
        $payment_value = $payment_value->create([
            'value' => $request->value,
            'payment_item_id' => $request->payment_item_id,
        ]);
        $payment_value->name = $payment_value->paymentItem->name;
        $payment_value->save();
        return $payment_value;
    }

    public function update(Request $request, PaymentValue $model)
    {
        $model->update([
            'value' => $request->value,
            'payment_item_id' => $request->payment_item_id,
        ]);
        $model->name = $model->paymentItem->name;
        $model->save();
        return $model;
    }

    public function fetchRecords()
    {
        return QueryBuilder::for($this->model->query())
            ->defaultSort('-id')
            ->allowedFilters(['payment_item_id']);
    }
    public function sync(PaymentValue $payment_value, string $method, array $ids)
    {
        if (method_exists($payment_value, $method)) {
            return $payment_value->{$method}()->sync($ids);
        }
        return false;
    }
}
