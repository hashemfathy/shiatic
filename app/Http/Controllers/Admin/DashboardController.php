<?php

namespace App\Http\Controllers\Admin;

use App\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Visit;
use Carbon\Carbon;

class DashboardController extends Controller
{
  public function index()
  {
    $totalClientCount = Client::all()->count();
    $maleClientCount = Client::where('gender', 'male')->count();
    $femaleClientCount = Client::where('gender', 'female')->count();
    $monthVisits = [];
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
      'unselectedVisitsCount' => Visit::where('specialist_id', 1)->count(),
    ];
    foreach (Visit::all() as $visit) {
      if (date("m", strtotime($visit->date)) == Carbon::now()->month && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
        array_push($monthVisits, $visit);
      }
    }
    $monthVisitsCount = [
      'totalVisitsCount' => collect($monthVisits)->count(),
      'ahmedAdelVisitsCount' => collect($monthVisits)->where('specialist_id', 2)->count(),
      'hanyVisitsCount' => collect($monthVisits)->where('specialist_id', 5)->count(),
      'HussienVisitsCount' => collect($monthVisits)->where('specialist_id', 3)->count(),
      'ezzatVisitsCount' => collect($monthVisits)->where('specialist_id', 4)->count(),
      'omarVisitsCount' => collect($monthVisits)->where('specialist_id', 6)->count(),
      'narimanVisitsCount' => collect($monthVisits)->where('specialist_id', 7)->count(),
      'alaaVisitsCount' => collect($monthVisits)->where('specialist_id', 9)->count(),
      'yaraVisitsCount' => collect($monthVisits)->where('specialist_id', 8)->count(),
      'unselectedVisitsCount' => collect($monthVisits)->where('specialist_id', 1)->count(),
    ];
    return view('backend.pages.dashboard.index', compact('totalClientCount', 'maleClientCount', 'femaleClientCount', 'visitsCount', 'monthVisitsCount'));
  }
}
