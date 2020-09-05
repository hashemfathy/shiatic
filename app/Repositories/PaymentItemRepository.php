<?php

namespace App\Repositories;

use App\PaymentItem;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;


class PaymentItemRepository
{
    public $model;

    public function __construct(PaymentItem $model)
    {
        $this->model = $model;
    }

    public function create(Request $request)
    {
        $client = new PaymentItem();
        return $client->create([
            'name' => $request->name,
        ]);
    }

    public function update(Request $request, PaymentItem $model)
    {
        $model->update([
            'name' => $request->name,
        ]);
        return $model;
    }

    public function fetchRecords()
    {
        return QueryBuilder::for($this->model->query())
            ->defaultSort('-id')
            ->allowedFilters(['name']);
    }
    public function sync(PaymentItem $payment_item, string $method, array $ids)
    {
        if (method_exists($payment_item, $method)) {
            return $payment_item->{$method}()->sync($ids);
        }
        return false;
    }
}
