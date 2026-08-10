<?php

use App\Models\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/new-client', function () {
    return view('new-client');
});
Route::post('/new-client', [\App\Http\Controllers\ClientPublicController::class, 'store'])
    ->name('public.client.store');
Route::get('/requests/accept', function () {
    Request::find(request()->get('id'))->update(['status'=>'confirmed']);
    return redirect()->back();
})->name('requests.accept');
Route::get('/requests/decline', function () {
    Request::find(request()->get('id'))->update(['status'=>'canceled']);
    return redirect()->back();
})->name('requests.decline');

Route::get('/booking', [\App\Http\Controllers\BookingController::class, 'index'])->name('booking.index');
Route::get('/booking/form', [\App\Http\Controllers\BookingController::class, 'form'])->name('booking.form');
Route::post('/booking/store', [\App\Http\Controllers\BookingController::class, 'store'])->name('booking.store');
Route::get('/booking/available-times', [\App\Http\Controllers\BookingController::class, 'availableTimes'])->name('booking.available-times');
Route::get('/booking/validate-time', [\App\Http\Controllers\BookingController::class, 'checkTimeAvailability'])->name('booking.validate-time');

Route::get('/artisan-panel', function () {
    $key = env('ARTISAN_PANEL_KEY');
    if (!$key || request('key') !== $key) {
        abort(403, 'Unauthorized access.');
    }
    return view('artisan-panel');
});

Route::post('/artisan-panel/run', function () {
    $key = env('ARTISAN_PANEL_KEY');
    if (!$key || request('key') !== $key) {
        return response()->json(['success' => false, 'message' => 'Unauthorized access.']);
    }

    $command = request('command');
    $allowedCommands = ['config:clear', 'package:discover', 'optimize:clear', 'optimize', 'migrate'];
    
    if (!in_array($command, $allowedCommands, true)) {
        return response()->json(['success' => false, 'message' => 'Command not allowed.']);
    }

    try {
        \Illuminate\Support\Facades\Artisan::call($command);
        $output = \Illuminate\Support\Facades\Artisan::output();
        return response()->json(['success' => true, 'output' => $output]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
});

