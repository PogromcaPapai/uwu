<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\FullCalenderController;
use App\Http\Controllers\PlaceController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// API do wyszukiwania miejsca
Route::get('place', [PlaceController::class, 'search']);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/{subpath?}', function ($subpath) {
    $url = 'http://217.168.143.76:9000/' . $subpath;
    $client = new Client();
    $response = $client->request('GET', $url);
    return response($response->getBody())
            ->withHeaders($response->getHeaders());
})->where('subpath', '.*');

Route::get('/calendar', function () {
    return view('calendar');
})->middleware(['auth'])->name('calendar');

Route::get('full-calender', [FullCalenderController::class, 'index'])->middleware(['auth']);
Route::post('full-calender/action', [FullCalenderController::class, 'action'])->middleware(['auth']);

Route::prefix('events')->group(function () {
    Route::get('index', [EventController::class, 'index'])->middleware(['auth'])->name('events');

    // add/calendar przyjmuje dodatkowe argumenty
    Route::get('add', [EventController::class, 'create'])->middleware(['auth'])->name('add_event');
    Route::get('add/calendar', [EventController::class, 'create_for_call'])->middleware(['auth'])->name('add_event_calendar');
    Route::post('add', [EventController::class, 'store'])->middleware(['auth']);
    Route::post('add/calendar', [EventController::class, 'store'])->middleware(['auth']);
    
    Route::get('edit/{id}', [EventController::class, 'edit'])->middleware(['auth']);
    Route::post('edit/{id}', [EventController::class, 'update'])->middleware(['auth']);

    Route::get('edit/{id}/delete', [EventController::class, 'destroy'])->middleware(['auth']);
});
require __DIR__.'/auth.php';