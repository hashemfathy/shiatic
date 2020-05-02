<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use App\Http\Resources\Admin\AdminVisitIndexResource;
use App\Services\Admin\AdminVisitService;
use App\Specialist;
use App\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinanceController extends Controller
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
        $income = [
            'todayVisitsIncome' => Visit::whereDate('date', '=', date("Y-m-d"))->sum('price'),
            'monthVisitsIncome' => Visit::whereMonth('date', '=', Carbon::now()->month)->whereYear('date', '=', Carbon::now()->year)->sum('price'),

        ];
        return view('backend.pages.finance.index', compact('income'));
    }
}
