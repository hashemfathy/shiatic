<?php

use App\Http\Resources\Admin\CategoryCollection;
use App\Models\User;
use App\Http\Resources\UserResource;
use App\Models\Category;
use App\Visit;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;


Route::group(['middleware' => 'auth'], function () {
    Route::get('', 'DashboardController@index')->name('index')->middleware('admin');
    Route::get('/test', function () {
        return DB::select("SELECT *
        FROM visits 
        WHERE EXTRACT(MONTH FROM date) = 06");
    });
    // collect(DB::select("SELECT * FROM visits WHERE date_part('month', date) = 03 AND EXTRACT(YEAR FROM date) = EXTRACT(YEAR FROM CURRENT_DATE) "))
    Route::get('clients/json', 'ClientController@getJson');
    Route::resource('clients', 'ClientController');
    Route::get('expenses/json', 'ExpensesController@getJson');
    Route::get('expenses/jsonToday', 'ExpensesController@getJsonToday');
    Route::resource('expenses', 'ExpensesController');
    Route::put('clients/toggle-status/{client}', 'ClientController@toggleStatus');
    Route::get('payment-items/json', 'PaymentItemController@getJson');
    Route::resource('payment-items', 'PaymentItemController');
    Route::get('visits/json', 'VisitController@getJson');
    Route::get('visits/today/json', 'VisitController@getTodayJson');
    Route::get('visits/today', 'VisitController@indexToday');
    Route::resource('visits', 'VisitController');
    Route::get('finance', 'FinanceController@index');
});

Route::get('whatsapp/send', function (Request $request) {
    $url = "https://messages-sandbox.nexmo.com/v0.1/messages";
    $params = ["to" => ["type" => "whatsapp", "number" => '201069597435'],
        "from" => ["type" => "whatsapp", "number" => "14157386170"],
        "message" => [
            "content" => [
                "type" => "text",
                "text" => "Your Today Pure Income is 300 EGP"
            ]
        ]
    ];
    $headers = ["Authorization" => "Basic " . base64_encode(env('NEXMO_API_KEY') . ":" . env('NEXMO_API_SECRET'))];

    $client = new \GuzzleHttp\Client();
    $response = $client->request('POST', $url, ["headers" => $headers, "json" => $params]);
    $data = $response->getBody();
    \Log::Info($data);

    return view('thanks');
});
// Route::get('categories-list', function () {
//     return CategoryCollection::collection(Category::parent()->with('withAllChildren')->get());
// });
// Route::get('tags/all', function () {
//     return  DB::table('tags')->select('title as text')->get();
// });
