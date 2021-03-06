<?php

namespace App\Repositories;

use App\Client;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;


class ClientRepository
{
    public $model;

    public function __construct(Client $model)
    {
        $this->model = $model;
    }

    public function create(Request $request)
    {
        $client = new Client();
        return $client->create([
            'name' => $request->name,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'code' => str_pad($request->code, 7, "0", STR_PAD_LEFT),
            'called' => false
        ]);
    }

    public function update(Request $request, Client $model)
    {
        $model->update([
            'name' => $request->name,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'code' => str_pad($request->code, 7, "0", STR_PAD_LEFT),
            'called' => $request->called
        ]);
        return $model;
    }

    public function fetchRecords()
    {
        return QueryBuilder::for($this->model->query())
            ->defaultSort('-code')
            ->allowedFilters(['name', 'code', 'phone'])
            ->allowedSorts(['name', 'gender', 'called']);
    }
    public function sync(Client $client, string $method, array $ids)
    {
        if (method_exists($client, $method)) {
            return $client->{$method}()->sync($ids);
        }
        return false;
    }
}
