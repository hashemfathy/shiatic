<?php

namespace App\Http\Controllers\Admin;

use App\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Visit;

class DashboardController extends Controller
{
  public function index()
  {
    $totalClientCount = Client::all()->count();
    $maleClientCount = Client::where('gender', 'male')->count();
    $femaleClientCount = Client::where('gender', 'female')->count();
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
    return view('backend.pages.dashboard.index', compact('totalClientCount', 'maleClientCount', 'femaleClientCount', 'visitsCount'));
  }
}
