<?php

namespace App\Http\Controllers\Admin;

use App\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
  public function index()
  {
    $totalClientCount = Client::all()->count();
    $maleClientCount = Client::where('gender', 'male')->count();
    $femaleClientCount = Client::where('gender', 'female')->count();
    return view('backend.pages.dashboard.index', compact('totalClientCount', 'maleClientCount', 'femaleClientCount'));
  }
}
