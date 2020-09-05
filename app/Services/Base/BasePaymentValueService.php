<?php

namespace App\Services\Base;

use App\PaymentValue;
use App\Repositories\PaymentValueRepository;
use Illuminate\Http\Request;


class BasePaymentValueService
{
    public $repository;
    public $query;

    public function __construct(PaymentValueRepository $repository)
    {
        $this->repository = $repository;
    }
    public function create(Request $request)
    {
        return $this->repository->create($request, [
            'value',
            'payment_item_id'
        ]);
    }
    public function update(Request $request, PaymentValue $payment_value)
    {
        return $this->repository->update($request, $payment_value, [
            'value',
            'payment_item_id'
        ]);
    }
    public function fetchRecords()
    {
        $this->query = $this->repository
            ->fetchRecords();

        return $this;
    }
    public function get()
    {
        return  $this->query->get();
    }
    public function where($column, $value)
    {
        $this->query->where($column, $value);
        return $this;
    }
    public function whereDate($column, $value)
    {
        $this->query->whereDate($column, $value);
        return $this;
    }
    public function paginate()
    {
        return $this->query->paginate((int) request()->get('per_page', config('tag.settings.pagination')));
    }
    public function sync(PaymentValue $payment_value, $method, $ids)
    {
        return $this->repository->sync($payment_value, $method, $ids);
    }
}
