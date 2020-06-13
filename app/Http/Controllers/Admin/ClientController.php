<?php

namespace App\Http\Controllers\Admin;

use App\Client;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\Admin\AdminClientIndexResource;
use App\Services\Admin\AdminClientService;
use App\Specialist;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public $adminClientService;

    public function __construct(AdminClientService $adminClientService)
    {
        $this->adminClientService = $adminClientService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.pages.client.index');
    }
    public function getJson(Request $request)
    {
        return AdminClientIndexResource::collection(
            $this->adminClientService->fetchRecords()->paginate()
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.pages.client.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateClientRequest $request)
    {
        $client =  $this->adminClientService->create($request);
        return response()->json([
            'data' => $client
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        $specialists = Specialist::all();
        return view('backend.pages.client.show', compact('client', 'specialists'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        return view('backend.pages.client.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateClientRequest $request, Client $client)
    {
        $client =  $this->adminClientService->update($request, $client);
        return response()->json([
            'data' => $client
        ], 200);
    }

    public function toggleStatus(Client $client)
    {
        return $this->adminClientService->toggleStatus($client);
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
