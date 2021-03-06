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
    $monthVisits = [];
    $janVisits = [];
    $febVisits = [];
    $marVisits = [];
    $aprVisits = [];
    $mayVisits = [];
    $junVisits = [];
    $julVisits = [];
    $augVisits = [];
    $sepVisits = [];
    $octVisits = [];
    $novVisits = [];
    $decVisits = [];
    $janNewClients = Client::whereYear('created_at',Carbon::now()->year)->whereMonth('created_at',1)->count();
    $febNewClients = Client::whereYear('created_at',Carbon::now()->year)->whereMonth('created_at',2)->count();
    $marNewClients = Client::whereYear('created_at',Carbon::now()->year)->whereMonth('created_at',3)->count();
    $aprNewClients = Client::whereYear('created_at',Carbon::now()->year)->whereMonth('created_at',4)->count();
    $mayNewClients = Client::whereYear('created_at',Carbon::now()->year)->whereMonth('created_at',5)->count();
    $junNewClients = Client::whereYear('created_at',Carbon::now()->year)->whereMonth('created_at',6)->count();
    $julNewClients = Client::whereYear('created_at',Carbon::now()->year)->whereMonth('created_at',7)->count();
    $augNewClients = Client::whereYear('created_at',Carbon::now()->year)->whereMonth('created_at',8)->count();
    $sepNewClients = Client::whereYear('created_at',Carbon::now()->year)->whereMonth('created_at',9)->count();
    $octNewClients = Client::whereYear('created_at',Carbon::now()->year)->whereMonth('created_at',10)->count();
    $novNewClients = Client::whereYear('created_at',Carbon::now()->year)->whereMonth('created_at',11)->count();
    $decNewClients = Client::whereYear('created_at',Carbon::now()->year)->whereMonth('created_at',12)->count();
    // ------
    $visitsCount = [
      'totalVisitsCount' => Visit::all()->count(),
      'ahmedAdelVisitsCount' => Visit::where('specialist_id', 2)->count(),
      'hanyVisitsCount' => Visit::where('specialist_id', 5)->count(),
      'ElbannaVisitsCount' => Visit::where('specialist_id', 3)->count(),
      'saadVisitsCount' => Visit::where('specialist_id', 4)->count(),
      'salemVisitsCount' => Visit::where('specialist_id', 6)->count(),
      'asmaaVisitsCount' => Visit::where('specialist_id', 7)->count(),
      'foadVisitsCount' => Visit::where('specialist_id', 9)->count(),
      'baherVisitsCount' => Visit::where('specialist_id', 8)->count(),
      'mohamedAdelVisitsCount' => Visit::where('specialist_id', 10)->count(),
      'belalVisitsCount' => Visit::where('specialist_id', 11)->count(),
      'unselectedVisitsCount' => Visit::where('specialist_id', 1)->count(),
    ];
    foreach (Visit::all() as $visit) {
      if (date("m", strtotime($visit->date)) == Carbon::now()->month && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
        array_push($monthVisits, $visit);
      }
    }
    foreach (Visit::all() as $visit) {
      if (date("m", strtotime($visit->date)) == 1 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
        array_push($janVisits, $visit);
      }
    }
    foreach (Visit::all() as $visit) {
      if (date("m", strtotime($visit->date)) == 2 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
        array_push($febVisits, $visit);
      }
    }
    foreach (Visit::all() as $visit) {
      if (date("m", strtotime($visit->date)) == 3 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
        array_push($marVisits, $visit);
      }
    }
    foreach (Visit::all() as $visit) {
      if (date("m", strtotime($visit->date)) == 4 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
        array_push($aprVisits, $visit);
      }
    }
    foreach (Visit::all() as $visit) {
      if (date("m", strtotime($visit->date)) == 5 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
        array_push($mayVisits, $visit);
      }
    }
    foreach (Visit::all() as $visit) {
      if (date("m", strtotime($visit->date)) == 6 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
        array_push($junVisits, $visit);
      }
    }
    foreach (Visit::all() as $visit) {
      if (date("m", strtotime($visit->date)) == 7 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
        array_push($julVisits, $visit);
      }
    }
    foreach (Visit::all() as $visit) {
      if (date("m", strtotime($visit->date)) == 8 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
        array_push($augVisits, $visit);
      }
    }
    foreach (Visit::all() as $visit) {
      if (date("m", strtotime($visit->date)) == 9 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
        array_push($sepVisits, $visit);
      }
    }
    foreach (Visit::all() as $visit) {
      if (date("m", strtotime($visit->date)) == 10 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
        array_push($octVisits, $visit);
      }
    }
    foreach (Visit::all() as $visit) {
      if (date("m", strtotime($visit->date)) == 11 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
        array_push($novVisits, $visit);
      }
    }
    foreach (Visit::all() as $visit) {
      if (date("m", strtotime($visit->date)) == 12 && date("Y", strtotime($visit->date)) == Carbon::now()->year) {
        array_push($decVisits, $visit);
      }
    }
    $monthVisitsCount = [
      'totalVisitsCount' => collect($monthVisits)->count(),
      'ahmedAdelVisitsCount' => collect($monthVisits)->where('specialist_id', 2)->count(),
      'hanyVisitsCount' => collect($monthVisits)->where('specialist_id', 5)->count(),
      'ElbannaVisitsCount' => collect($monthVisits)->where('specialist_id', 3)->count(),
      'saadVisitsCount' => collect($monthVisits)->where('specialist_id', 4)->count(),
      'salemVisitsCount' => collect($monthVisits)->where('specialist_id', 6)->count(),
      'asmaaVisitsCount' => collect($monthVisits)->where('specialist_id', 7)->count(),
      'foadVisitsCount' => collect($monthVisits)->where('specialist_id', 9)->count(),
      'baherVisitsCount' => collect($monthVisits)->where('specialist_id', 8)->count(),
      'mohamedAdelVisitsCount' => collect($monthVisits)->where('specialist_id', 10)->count(),
      'belalVisitsCount' => collect($monthVisits)->where('specialist_id', 11)->count(),
      'unselectedVisitsCount' => collect($monthVisits)->where('specialist_id', 1)->count(),
    ];
    $statics = [
      'jan' => collect($janVisits)->count(),
      'feb' => collect($febVisits)->count(),
      'mar' => collect($marVisits)->count(),
      'apr' => collect($aprVisits)->count(),
      'may' => collect($mayVisits)->count(),
      'jun' => collect($junVisits)->count(),
      'jul' => collect($julVisits)->count(),
      'aug' => collect($augVisits)->count(),
      'sep' => collect($sepVisits)->count(),
      'oct' => collect($octVisits)->count(),
      'nov' => collect($novVisits)->count(),
      'dec' => collect($decVisits)->count(),
    ];
    $new_clients = [
      'jan' => $janNewClients,
      'feb' => $febNewClients,
      'mar' => $marNewClients,
      'apr' => $aprNewClients,
      'may' => $mayNewClients,
      'jun' => $junNewClients,
      'jul' => $julNewClients,
      'aug' => $augNewClients,
      'sep' => $sepNewClients,
      'oct' => $octNewClients,
      'nov' => $novNewClients,
      'dec' => $decNewClients,
    ];
    return view('backend.pages.dashboard.index',
     compact('totalClientCount', 'maleClientCount', 'femaleClientCount', 'visitsCount', 'monthVisitsCount', 'statics','new_clients'));
  }
}
