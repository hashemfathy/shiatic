<?php

namespace App\Http\Controllers\Admin;

use App\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Visit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
  public function index()
  {
    $totalClientCount = Client::all()->count();
    $maleClientCount = Client::where('gender', 'male')->count();
    $femaleClientCount = Client::where('gender', 'female')->count();
    $monthVisits = collect(DB::select("SELECT * FROM visits WHERE date >= LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY - INTERVAL 1 MONTH
    AND date < LAST_DAY(CURRENT_DATE) + INTERVAL 1 DAY"));
    $janVisits = collect(DB::select("SELECT * FROM visits WHERE EXTRACT(MONTH FROM date) = 01 AND EXTRACT(YEAR FROM date) = EXTRACT(YEAR FROM CURRENT_DATE) "));
    $febVisits = collect(DB::select("SELECT * FROM visits WHERE EXTRACT(MONTH FROM date) = 02 AND EXTRACT(YEAR FROM date) = EXTRACT(YEAR FROM CURRENT_DATE) "));
    $marVisits = collect(DB::select("SELECT * FROM visits WHERE EXTRACT(MONTH FROM date) = 03 AND EXTRACT(YEAR FROM date) = EXTRACT(YEAR FROM CURRENT_DATE) "));
    $aprVisits = collect(DB::select("SELECT * FROM visits WHERE EXTRACT(MONTH FROM date) = 04 AND EXTRACT(YEAR FROM date) = EXTRACT(YEAR FROM CURRENT_DATE) "));
    $mayVisits = collect(DB::select("SELECT * FROM visits WHERE EXTRACT(MONTH FROM date) = 05 AND EXTRACT(YEAR FROM date) = EXTRACT(YEAR FROM CURRENT_DATE) "));
    $junVisits = collect(DB::select("SELECT * FROM visits WHERE EXTRACT(MONTH FROM date) = 06 AND EXTRACT(YEAR FROM date) = EXTRACT(YEAR FROM CURRENT_DATE) "));
    $julVisits = collect(DB::select("SELECT * FROM visits WHERE EXTRACT(MONTH FROM date) = 07 AND EXTRACT(YEAR FROM date) = EXTRACT(YEAR FROM CURRENT_DATE) "));
    $augVisits = collect(DB::select("SELECT * FROM visits WHERE EXTRACT(MONTH FROM date) = 08 AND EXTRACT(YEAR FROM date) = EXTRACT(YEAR FROM CURRENT_DATE) "));
    $sepVisits = collect(DB::select("SELECT * FROM visits WHERE EXTRACT(MONTH FROM date) = 09 AND EXTRACT(YEAR FROM date) = EXTRACT(YEAR FROM CURRENT_DATE) "));
    $octVisits = collect(DB::select("SELECT * FROM visits WHERE EXTRACT(MONTH FROM date) = 10 AND EXTRACT(YEAR FROM date) = EXTRACT(YEAR FROM CURRENT_DATE) "));
    $novVisits = collect(DB::select("SELECT * FROM visits WHERE EXTRACT(MONTH FROM date) = 11 AND EXTRACT(YEAR FROM date) = EXTRACT(YEAR FROM CURRENT_DATE) "));
    $decVisits = collect(DB::select("SELECT * FROM visits WHERE EXTRACT(MONTH FROM date) = 12 AND EXTRACT(YEAR FROM date) = EXTRACT(YEAR FROM CURRENT_DATE) "));
    // ------
    $visitsCount = [
      'totalVisitsCount' => Visit::all()->count(),
      'ahmedAdelVisitsCount' => Visit::where('specialist_id', 2)->count(),
      'hanyVisitsCount' => Visit::where('specialist_id', 5)->count(),
      'HussienVisitsCount' => Visit::where('specialist_id', 3)->count(),
      'ezzatVisitsCount' => Visit::where('specialist_id', 4)->count(),
      'omarVisitsCount' => Visit::where('specialist_id', 6)->count(),
      'narimanVisitsCount' => Visit::where('specialist_id', 7)->count(),
      'alaaVisitsCount' => Visit::where('specialist_id', 9)->count(),
      'yaraVisitsCount' => Visit::where('specialist_id', 8)->count(),
      'mohamedAdelVisitsCount' => Visit::where('specialist_id', 10)->count(),
      'baherVisitsCount' => Visit::where('specialist_id', 11)->count(),
      'unselectedVisitsCount' => Visit::where('specialist_id', 1)->count(),
    ];
    // foreach (Visit::all() as $visit) {
    //   if (date("m", strtotime($visit->date)) == Carbon::now()->month && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
    //     array_push($monthVisits, $visit);
    //   }
    // }
    // foreach (Visit::all() as $visit) {
    //   if (date("m", strtotime($visit->date)) == 1 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
    //     array_push($janVisits, $visit);
    //   }
    // }
    // foreach (Visit::all() as $visit) {
    //   if (date("m", strtotime($visit->date)) == 2 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
    //     array_push($febVisits, $visit);
    //   }
    // }
    // foreach (Visit::all() as $visit) {
    //   if (date("m", strtotime($visit->date)) == 3 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
    //     array_push($marVisits, $visit);
    //   }
    // }
    // foreach (Visit::all() as $visit) {
    //   if (date("m", strtotime($visit->date)) == 4 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
    //     array_push($aprVisits, $visit);
    //   }
    // }
    // foreach (Visit::all() as $visit) {
    //   if (date("m", strtotime($visit->date)) == 5 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
    //     array_push($mayVisits, $visit);
    //   }
    // }
    // foreach (Visit::all() as $visit) {
    //   if (date("m", strtotime($visit->date)) == 6 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
    //     array_push($junVisits, $visit);
    //   }
    // }
    // foreach (Visit::all() as $visit) {
    //   if (date("m", strtotime($visit->date)) == 7 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
    //     array_push($julVisits, $visit);
    //   }
    // }
    // foreach (Visit::all() as $visit) {
    //   if (date("m", strtotime($visit->date)) == 8 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
    //     array_push($augVisits, $visit);
    //   }
    // }
    // foreach (Visit::all() as $visit) {
    //   if (date("m", strtotime($visit->date)) == 9 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
    //     array_push($sepVisits, $visit);
    //   }
    // }
    // foreach (Visit::all() as $visit) {
    //   if (date("m", strtotime($visit->date)) == 10 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
    //     array_push($octVisits, $visit);
    //   }
    // }
    // foreach (Visit::all() as $visit) {
    //   if (date("m", strtotime($visit->date)) == 11 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
    //     array_push($novVisits, $visit);
    //   }
    // }
    // foreach (Visit::all() as $visit) {
    //   if (date("m", strtotime($visit->date)) == 12 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
    //     array_push($decVisits, $visit);
    //   }
    // }
    $monthVisitsCount = [
      'totalVisitsCount' => $monthVisits->count(),
      'ahmedAdelVisitsCount' => $monthVisits->where('specialist_id', 2)->count(),
      'hanyVisitsCount' => $monthVisits->where('specialist_id', 5)->count(),
      'HussienVisitsCount' => $monthVisits->where('specialist_id', 3)->count(),
      'ezzatVisitsCount' => $monthVisits->where('specialist_id', 4)->count(),
      'omarVisitsCount' => $monthVisits->where('specialist_id', 6)->count(),
      'narimanVisitsCount' => $monthVisits->where('specialist_id', 7)->count(),
      'alaaVisitsCount' => $monthVisits->where('specialist_id', 9)->count(),
      'yaraVisitsCount' => $monthVisits->where('specialist_id', 8)->count(),
      'mohamedAdelVisitsCount' => $monthVisits->where('specialist_id', 10)->count(),
      'baherVisitsCount' => $monthVisits->where('specialist_id', 11)->count(),
      'unselectedVisitsCount' => $monthVisits->where('specialist_id', 1)->count(),
    ];
    $statics = [
      'janVisits' => $janVisits->count(),
      'febVisits' => $febVisits->count(),
      'marVisits' => $marVisits->count(),
      'aprVisits' => $aprVisits->count(),
      'mayVisits' => $mayVisits->count(),
      'junVisits' => $junVisits->count(),
      'julVisits' => $julVisits->count(),
      'augVisits' => $augVisits->count(),
      'sepVisits' => $sepVisits->count(),
      'octVisits' => $octVisits->count(),
      'novVisits' => $novVisits->count(),
      'decVisits' => $decVisits->count(),
    ];
    return view('backend.pages.dashboard.index', compact('totalClientCount', 'maleClientCount', 'femaleClientCount', 'visitsCount', 'monthVisitsCount', 'statics'));
  }
}
