<?php

namespace App\Repositories;

use App\Visit;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;


class VisitRepository
{
    public $model;

    public function __construct(Visit $model)
    {
        $this->model = $model;
    }

    public function create(Request $request)
    {
        $visit = new Visit();
        return $visit->create([
            'client_name' => $request->client_name,
            'complaint' => $request->complaint,
            'price' => $request->price,
            'date' => $request->date,
            'hour' => $request->hour,
            'duration' => $request->duration,
            'client_id' => $request->client_id
        ]);
    }

    public function update(Request $request, Visit $model)
    {
        $model->update([
            'client_name' => $request->client_name,
            'complaint' => $request->complaint,
            'price' => $request->price,
            'date' => $request->date,
            'hour' => $request->hour,
            'duration' => $request->duration,
            'client_id' => $request->client_id
        ]);
        return $model;
    }

    public function fetchRecords()
    {
        return QueryBuilder::for($this->model->query())
            ->defaultSort('-id')
            ->allowedFilters(['client_name', 'date', 'complaint'])
            ->allowedSorts(['date', 'complaint']);
    }
    public function fetchTodayRecords()
    {
        return QueryBuilder::for($this->model->query())->whereDate('date', '=', date("Y-m-d"))
            ->defaultSort('date')
            ->allowedFilters(['client_name', 'date', 'complaint'])
            ->allowedSorts(['date', 'complaint']);
    }
    public function sync(Visit $visit, string $method, array $ids)
    {
        if (method_exists($visit, $method)) {
            return $visit->{$method}()->sync($ids);
        }
        return false;
    }
}
