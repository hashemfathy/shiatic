<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use App\Http\Resources\Admin\AdminVisitIndexResource;
use App\Services\Admin\AdminVisitService;
use App\Specialist;
use App\Visit;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public $adminVisitService;

    public function __construct(AdminVisitService $adminVisitService)
    {
        $this->adminVisitService = $adminVisitService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('backend.pages.visit.index');
    }
    public function indexToday()
    {
        return view('backend.pages.visit.today');
    }
    public function getJson(Request $request)
    {
        return AdminVisitIndexResource::collection(
            $this->adminVisitService->fetchRecords()->paginate()
        );
    }
    public function getTodayJson()
    {
        return AdminVisitIndexResource::collection(
            $this->adminVisitService->fetchTodayRecords()->paginate()
        );
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $specialists = Specialist::all();
        return view('backend.pages.Visit.create', compact('specialists'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateVisitRequest $request)
    {
        $visit =  $this->adminVisitService->create($request);
        return response()->json([
            'data' => $visit
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Visit $visit)
    {
        return view('backend.pages.Visit.show', compact('Visit'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Visit $visit)
    {
        $specialists = Specialist::all();
        return view('backend.pages.visit.edit', compact('visit', 'specialists'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVisitRequest $request, Visit $visit)
    {
        $visit =  $this->adminVisitService->update($request, $visit);
        return response()->json([
            'data' => $visit
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
