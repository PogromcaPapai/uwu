<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\ModEventController;
use App\Http\Controllers\ModUserController;
use Illuminate\Support\Facades\Route;
use GuzzleHttp\Client;
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
    $url = 'http://172.24.206.101:9000/' . $subpath;
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

Route::prefix('admin')->group(function () {
    Route::prefix('events')->group(function () {
        Route::get('index', [ModEventController::class, 'index'])->middleware(['auth'])->name('admin_events');
        
        Route::get('edit/{id}', [ModEventController::class, 'edit'])->middleware(['auth']);
        Route::post('edit/{id}', [ModEventController::class, 'update'])->middleware(['auth']);
    
        Route::get('edit/{id}/delete', [ModEventController::class, 'destroy'])->middleware(['auth']);
        
    });

    Route::prefix('users')->group(function () {
        Route::get('index', [ModUserController::class, 'index'])->middleware(['auth'])->name('admin_users');
        Route::post('edit', [ModUserController::class, 'post'])->middleware(['auth']);
        Route::get('edit/{id}/delete', [ModUserController::class, 'delete'])->middleware(['auth']);
    });
});

require __DIR__.'/auth.php';