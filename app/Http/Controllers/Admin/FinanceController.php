<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use App\Http\Resources\Admin\AdminVisitIndexResource;
use App\PaymentItem;
use App\PaymentValue;
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
        $todayOutgoings = PaymentValue::whereDate('created_at', Carbon::today())->sum('value');
        $todayPureIncome = $todayVisitsIncome - $todayOutgoings;
        $monthOutgoings = PaymentValue::whereMonth('created_at', '=', date('m'))->whereYear('created_at', '=', date('Y'))->sum('value');
        $monthOutgoings_items = DB::table('payment_values')
            ->whereMonth('created_at', '=', date('m'))
            ->whereYear('created_at', '=', date('Y'))
            ->select(DB::raw('SUM(value) as value'), 'name')
            ->groupBy('name')->get();
        $income = [
            'todayVisitsIncome' => $todayVisitsIncome,
            'todayOutgoings' => $todayOutgoings,
            'todayPureIncome' => $todayPureIncome,
            'monthVisitsIncome' => $monthVisitsIncome,
            'monthOutgoings' => $monthOutgoings,
            'monthOutgoings_items' => $monthOutgoings_items,
        ];
        return view('backend.pages.finance.index', compact('income'));
    }
}
