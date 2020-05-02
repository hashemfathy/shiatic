<?php

namespace App\Services\Base;

use App\Repositories\VisitRepository;
use App\Visit;
use Illuminate\Http\Request;


class BaseVisitService
{
    public $repository;
    public $query;

    public function __construct(VisitRepository $repository)
    {
        $this->repository = $repository;
    }
    public function create(Request $request)
    {
        return $this->repository->create($request, [
            'client_name', 'complaint', 'price', 'date', 'hour', 'duration', 'client_id', 'specialist_id'
        ]);
    }
    public function update(Request $request, Visit $visit)
    {
        return $this->repository->update($request, $visit, [
            'client_name', 'complaint', 'price', 'date', 'hour', 'duration', 'client_id', 'specialist_id'
        ]);
    }
    public function fetchRecords()
    {
        $this->query = $this->repository
            ->fetchRecords();

        return $this;
    }
    public function fetchTodayRecords()
    {
        $this->query = $this->repository
            ->fetchTodayRecords();

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
    public function sync(Visit $visit, $method, $ids)
    {
        return $this->repository->sync($visit, $method, $ids);
    }
}
