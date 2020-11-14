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
        $janVisitsIncome = 0;
        $febVisitsIncome = 0;
        $marVisitsIncome = 0;
        $aprVisitsIncome = 0;
        $mayVisitsIncome = 0;
        $junVisitsIncome = 0;
        $julVisitsIncome = 0;
        $augVisitsIncome = 0;
        $sepVisitsIncome = 0;
        $octVisitsIncome = 0;
        $novVisitsIncome = 0;
        $decVisitsIncome = 0;
        foreach (Visit::all()->where("date", '=', date("Y-m-d")) as $visit) {
            $todayVisitsIncome += $visit->price;
        }
        foreach (Visit::all() as $visit) {
            if (date("Y", strtotime($visit->date)) == Carbon::now()->year && date("m", strtotime($visit->date)) == Carbon::now()->month ) {
                $monthVisitsIncome += $visit->price;
            }
        }
        foreach (Visit::all() as $visit) {
            if (date("Y", strtotime($visit->date)) == Carbon::now()->year && date("m", strtotime($visit->date)) == 1 ) {
                $janVisitsIncome += $visit->price;
            }
        }
        foreach (Visit::all() as $visit) {
            if (date("Y", strtotime($visit->date)) == Carbon::now()->year && date("m", strtotime($visit->date)) == 2 ) {
                $febVisitsIncome += $visit->price;
            }
        }
        foreach (Visit::all() as $visit) {
            if (date("Y", strtotime($visit->date)) == Carbon::now()->year && date("m", strtotime($visit->date)) == 3 ) {
                $marVisitsIncome += $visit->price;
            }
        }
        foreach (Visit::all() as $visit) {
            if (date("Y", strtotime($visit->date)) == Carbon::now()->year && date("m", strtotime($visit->date)) == 4 ) {
                $aprVisitsIncome += $visit->price;
            }
        }
        foreach (Visit::all() as $visit) {
            if (date("Y", strtotime($visit->date)) == Carbon::now()->year && date("m", strtotime($visit->date)) == 5 ) {
                $mayVisitsIncome += $visit->price;
            }
        }
        foreach (Visit::all() as $visit) {
            if (date("Y", strtotime($visit->date)) == Carbon::now()->year && date("m", strtotime($visit->date)) == 6 ) {
                $junVisitsIncome += $visit->price;
            }
        }
        foreach (Visit::all() as $visit) {
            if (date("Y", strtotime($visit->date)) == Carbon::now()->year && date("m", strtotime($visit->date)) == 7 ) {
                $julVisitsIncome += $visit->price;
            }
        }
        foreach (Visit::all() as $visit) {
            if (date("Y", strtotime($visit->date)) == Carbon::now()->year && date("m", strtotime($visit->date)) == 8 ) {
                $augVisitsIncome += $visit->price;
            }
        }
        foreach (Visit::all() as $visit) {
            if (date("Y", strtotime($visit->date)) == Carbon::now()->year && date("m", strtotime($visit->date)) == 9 ) {
                $sepVisitsIncome += $visit->price;
            }
        }
        foreach (Visit::all() as $visit) {
            if (date("Y", strtotime($visit->date)) == Carbon::now()->year && date("m", strtotime($visit->date)) == 10 ) {
                $octVisitsIncome += $visit->price;
            }
        }
        foreach (Visit::all() as $visit) {
            if (date("Y", strtotime($visit->date)) == Carbon::now()->year && date("m", strtotime($visit->date)) == 11 ) {
                $novVisitsIncome += $visit->price;
            }
        }
        foreach (Visit::all() as $visit) {
            if (date("Y", strtotime($visit->date)) == Carbon::now()->year && date("m", strtotime($visit->date)) == 12 ) {
                $decVisitsIncome += $visit->price;
            }
        }
        $todayOutgoings = PaymentValue::whereDate('created_at', Carbon::today())->sum('value');
        $todayPureIncome = $todayVisitsIncome - $todayOutgoings;
        $monthOutgoings = PaymentValue::whereMonth('created_at', '=', date('m'))->whereYear('created_at', '=', date('Y'))->sum('value');
        $janOutgoings = PaymentValue::whereMonth('created_at', '=', 1)->whereYear('created_at', '=', date('Y'))->sum('value');
        $febOutgoings = PaymentValue::whereMonth('created_at', '=', 2)->whereYear('created_at', '=', date('Y'))->sum('value');
        $marOutgoings = PaymentValue::whereMonth('created_at', '=', 3)->whereYear('created_at', '=', date('Y'))->sum('value');
        $aprOutgoings = PaymentValue::whereMonth('created_at', '=', 4)->whereYear('created_at', '=', date('Y'))->sum('value');
        $mayOutgoings = PaymentValue::whereMonth('created_at', '=', 5)->whereYear('created_at', '=', date('Y'))->sum('value');
        $junOutgoings = PaymentValue::whereMonth('created_at', '=', 6)->whereYear('created_at', '=', date('Y'))->sum('value');
        $julOutgoings = PaymentValue::whereMonth('created_at', '=', 7)->whereYear('created_at', '=', date('Y'))->sum('value');
        $augOutgoings = PaymentValue::whereMonth('created_at', '=', 8)->whereYear('created_at', '=', date('Y'))->sum('value');
        $sepOutgoings = PaymentValue::whereMonth('created_at', '=', 9)->whereYear('created_at', '=', date('Y'))->sum('value');
        $octOutgoings = PaymentValue::whereMonth('created_at', '=', 10)->whereYear('created_at', '=', date('Y'))->sum('value');
        $novOutgoings = PaymentValue::whereMonth('created_at', '=', 11)->whereYear('created_at', '=', date('Y'))->sum('value');
        $decOutgoings = PaymentValue::whereMonth('created_at', '=', 12)->whereYear('created_at', '=', date('Y'))->sum('value');
        $monthOutgoings_items = DB::table('payment_values')
            ->whereYear('created_at', '=', date('Y'))
            ->whereMonth('created_at', '=', date('m'))
            ->select(DB::raw('SUM(value) as value'), 'name')
            ->groupBy('name')->get();
        $janOutgoings_items = DB::table('payment_values')
            ->whereYear('created_at', '=', date('Y'))
            ->whereMonth('created_at', '=', 1)
            ->select(DB::raw('SUM(value) as value'), 'name')
            ->groupBy('name')->get();
        $febOutgoings_items = DB::table('payment_values')
            ->whereYear('created_at', '=', date('Y'))
            ->whereMonth('created_at', '=', 2)
            ->select(DB::raw('SUM(value) as value'), 'name')
            ->groupBy('name')->get();
        $marOutgoings_items = DB::table('payment_values')
            ->whereYear('created_at', '=', date('Y'))
            ->whereMonth('created_at', '=', 3)
            ->select(DB::raw('SUM(value) as value'), 'name')
            ->groupBy('name')->get();
        $aprOutgoings_items = DB::table('payment_values')
            ->whereYear('created_at', '=', date('Y'))
            ->whereMonth('created_at', '=', 4)
            ->select(DB::raw('SUM(value) as value'), 'name')
            ->groupBy('name')->get();
        $mayOutgoings_items = DB::table('payment_values')
            ->whereYear('created_at', '=', date('Y'))
            ->whereMonth('created_at', '=', 5)
            ->select(DB::raw('SUM(value) as value'), 'name')
            ->groupBy('name')->get();
        $junOutgoings_items = DB::table('payment_values')
            ->whereYear('created_at', '=', date('Y'))
            ->whereMonth('created_at', '=', 6)
            ->select(DB::raw('SUM(value) as value'), 'name')
            ->groupBy('name')->get();
        $julOutgoings_items = DB::table('payment_values')
            ->whereYear('created_at', '=', date('Y'))
            ->whereMonth('created_at', '=', 7)
            ->select(DB::raw('SUM(value) as value'), 'name')
            ->groupBy('name')->get();
        $augOutgoings_items = DB::table('payment_values')
            ->whereYear('created_at', '=', date('Y'))
            ->whereMonth('created_at', '=', 8)
            ->select(DB::raw('SUM(value) as value'), 'name')
            ->groupBy('name')->get();
        $sepOutgoings_items = DB::table('payment_values')
            ->whereYear('created_at', '=', date('Y'))
            ->whereMonth('created_at', '=', 9)
            ->select(DB::raw('SUM(value) as value'), 'name')
            ->groupBy('name')->get();
        $octOutgoings_items = DB::table('payment_values')
            ->whereYear('created_at', '=', date('Y'))
            ->whereMonth('created_at', '=', 10)
            ->select(DB::raw('SUM(value) as value'), 'name')
            ->groupBy('name')->get();
        $novOutgoings_items = DB::table('payment_values')
            ->whereYear('created_at', '=', date('Y'))
            ->whereMonth('created_at', '=', 11)
            ->select(DB::raw('SUM(value) as value'), 'name')
            ->groupBy('name')->get();
        $decOutgoings_items = DB::table('payment_values')
            ->whereYear('created_at', '=', date('Y'))
            ->whereMonth('created_at', '=', 12)
            ->select(DB::raw('SUM(value) as value'), 'name')
            ->groupBy('name')->get();
        $income = [
            'todayVisitsIncome' => $todayVisitsIncome,
            'todayOutgoings' => $todayOutgoings,
            'todayPureIncome' => $todayPureIncome,
            'statistics'=>[
                [
                    'name_income'=>'Month Income',
                    'name_outgoing'=>'Month Outgoing',
                    'name_pure'=>'Month Pure Income',
                    'pure_income'=>$monthVisitsIncome-$monthOutgoings,
                    'visitsIncome' => $monthVisitsIncome,
                    'outgoings' => $monthOutgoings,
                    'outgoings_items' => $monthOutgoings_items,
                ],
                [
                    'name_income'=>'Jan Income',
                    'name_outgoing'=>'Jan Outgoing',
                    'name_pure'=>'Jan Pure Income',
                    'pure_income'=>$janVisitsIncome-$janOutgoings,
                    'visitsIncome' => $janVisitsIncome,
                    'outgoings' => $janOutgoings,
                    'outgoings_items' => $janOutgoings_items,
                ],
                [
                    'name_income'=>'Feb Income',
                    'name_outgoing'=>'Feb Outgoing',
                    'name_pure'=>'Feb Pure Income',
                    'pure_income'=>$febVisitsIncome-$febOutgoings,
                    'visitsIncome' => $febVisitsIncome,
                    'outgoings' => $febOutgoings,
                    'outgoings_items' => $febOutgoings_items,
                ],
                [
                    'name_income'=>'Mar Income',
                    'name_outgoing'=>'Mar Outgoing',
                    'name_pure'=>'Mar Pure Income',
                    'pure_income'=>$marVisitsIncome-$marOutgoings,
                    'visitsIncome' => $marVisitsIncome,
                    'outgoings' => $marOutgoings,
                    'outgoings_items' => $marOutgoings_items,
                ],
                [
                    'name_income'=>'Apr Income',
                    'name_outgoing'=>'Apr Outgoing',
                    'name_pure'=>'Apr Pure Income',
                    'pure_income'=>$aprVisitsIncome-$aprOutgoings,
                    'visitsIncome' => $aprVisitsIncome,
                    'outgoings' => $aprOutgoings,
                    'outgoings_items' => $aprOutgoings_items,
                ],
                [
                    'name_income'=>'May Income',
                    'name_outgoing'=>'May Outgoing',
                    'name_pure'=>'May Pure Income',
                    'pure_income'=>$mayVisitsIncome-$mayOutgoings,
                    'visitsIncome' => $mayVisitsIncome,
                    'outgoings' => $mayOutgoings,
                    'outgoings_items' => $mayOutgoings_items,
                ],
                [
                    'name_income'=>'Jun Income',
                    'name_outgoing'=>'Jun Outgoing',
                    'name_pure'=>'Jun Pure Income',
                    'pure_income'=>$junVisitsIncome-$junOutgoings,
                    'visitsIncome' => $junVisitsIncome,
                    'outgoings' => $junOutgoings,
                    'outgoings_items' => $junOutgoings_items,
                ],
                [
                    'name_income'=>'Jul Income',
                    'name_outgoing'=>'Jul Outgoing',
                    'name_pure'=>'Jul Pure Income',
                    'pure_income'=>$julVisitsIncome-$julOutgoings,
                    'visitsIncome' => $julVisitsIncome,
                    'outgoings' => $julOutgoings,
                    'outgoings_items' => $julOutgoings_items,
                ],
                [
                    'name_income'=>'Aug Income',
                    'name_outgoing'=>'Aug Outgoing',
                    'name_pure'=>'Aug Pure Income',
                    'pure_income'=>$augVisitsIncome-$augOutgoings,
                    'visitsIncome' => $augVisitsIncome,
                    'outgoings' => $augOutgoings,
                    'outgoings_items' => $augOutgoings_items,
                ],
                [
                    'name_income'=>'Sep Income',
                    'name_outgoing'=>'Sep Outgoing',
                    'name_pure'=>'Sep Pure Income',
                    'pure_income'=>$sepVisitsIncome-$sepOutgoings,
                    'visitsIncome' => $sepVisitsIncome,
                    'outgoings' => $sepOutgoings,
                    'outgoings_items' => $sepOutgoings_items,
                ],
                [
                    'name_income'=>'Oct Income',
                    'name_outgoing'=>'Oct Outgoing',
                    'name_pure'=>'Oct Pure Income',
                    'pure_income'=>$octVisitsIncome-$octOutgoings,
                    'visitsIncome' => $octVisitsIncome,
                    'outgoings' => $octOutgoings,
                    'outgoings_items' => $octOutgoings_items,
                ],
                [
                    'name_income'=>'Nov Income',
                    'name_outgoing'=>'Nov Outgoing',
                    'name_pure'=>'Nov Pure Income',
                    'pure_income'=>$novVisitsIncome-$novOutgoings,
                    'visitsIncome' => $novVisitsIncome,
                    'outgoings' => $novOutgoings,
                    'outgoings_items' => $novOutgoings_items,
                ],
                [
                    'name_income'=>'Dec Income',
                    'name_outgoing'=>'Dec Outgoing',
                    'name_pure'=>'Dec Pure Income',
                    'pure_income'=>$decVisitsIncome-$decOutgoings,
                    'visitsIncome' => $decVisitsIncome,
                    'outgoings' => $decOutgoings,
                    'outgoings_items' => $decOutgoings_items,
                ]
            ]
        ];
        return view('backend.pages.finance.index', compact('income'));
    }
}
