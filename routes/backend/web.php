<?php

use App\Http\Resources\Admin\CategoryCollection;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Models\Category;
use App\Visit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

Route::group(['middleware' => 'auth'], function () {
    Route::get('', 'DashboardController@index')->name('index');
    Route::get('clients/json', 'ClientController@getJson');
    Route::resource('clients', 'ClientController');
    Route::get('visits/json', 'VisitController@getJson');
    Route::get('visits/today/json', 'VisitController@getTodayJson');
    Route::get('visits/today', 'VisitController@indexToday');
    Route::resource('visits', 'VisitController');
    Route::get('finance', 'FinanceController@index');
});

// Route::get('users', function (Request $request) {
//     return UserResource::collection(User::all());
// });
Route::get('test', function () {
    return DB::table('visits')
        ->whereRaw("extract(month from date)", "=", Carbon::now()->month)
        ->get();
});
// Route::get('tags/all', function () {
//     return  DB::table('tags')->select('title as text')->get();
// });
