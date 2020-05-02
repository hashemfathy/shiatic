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
use Illuminate\Support\Facades\DB;

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
        $todayVisitsIncome = 0;
        $monthVisitsIncome = 0;
        foreach (Visit::all()->where("date", '=', date("Y-m-d")) as $visit) {
            $todayVisitsIncome += $visit->price;
        }
        foreach (Visit::all() as $visit) {
            if (date("m", strtotime($visit->date)) == Carbon::now()->month && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
                $monthVisitsIncome += $visit->price;
            }
        }
        $income = [
            'todayVisitsIncome' => $todayVisitsIncome,
            'monthVisitsIncome' => $monthVisitsIncome
        ];
        return view('backend.pages.finance.index', compact('income'));
    }
}
