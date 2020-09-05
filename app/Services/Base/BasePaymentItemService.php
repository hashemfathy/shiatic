<?php

namespace App\Services\Base;

use App\PaymentItem;
use App\Repositories\PaymentItemRepository;
use Illuminate\Http\Request;


class BasePaymentItemService
{
    public $repository;
    public $query;

    public function __construct(PaymentItemRepository $repository)
    {
        $this->repository = $repository;
    }
    public function create(Request $request)
    {
        return $this->repository->create($request, [
            'name'
        ]);
    }
    public function update(Request $request, PaymentItem $payment_item)
    {
        return $this->repository->update($request, $payment_item, [
            'name'
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

    public function paginate()
    {
        return $this->query->paginate((int) request()->get('per_page', config('tag.settings.pagination')));
    }
    public function sync(PaymentItem $payment_item, $method, $ids)
    {
        return $this->repository->sync($payment_item, $method, $ids);
    }
}
