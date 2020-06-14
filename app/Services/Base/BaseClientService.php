<?php

namespace App\Services\Base;

use App\Client;
use App\Repositories\ClientRepository;
use Illuminate\Http\Request;


class BaseClientService
{
    public $repository;
    public $query;

    public function __construct(ClientRepository $repository)
    {
        $this->repository = $repository;
    }
    public function create(Request $request)
    {
        return $this->repository->create($request, [
            'name', 'gender', 'phone', 'code'
        ]);
    }
    public function update(Request $request, Client $client)
    {
        return $this->repository->update($request, $client, [
            'name', 'gender', 'phone', 'code', 'called'
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
    public function sync(Client $client, $method, $ids)
    {
        return $this->repository->sync($client, $method, $ids);
    }

    public function toggleStatus(Client $client)
    {
        $client->called = !$client->called;
        $client->update();
        return $client;
    }
}
