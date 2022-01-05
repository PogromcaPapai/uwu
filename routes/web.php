<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;
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

Route::get('place', [PlaceController::class, 'search']);
Route::get('/', function () {
    return view('welcome');
});

Route::get('/calendar', function () {
    return view('calendar');
})->middleware(['auth'])->name('calendar');

Route::get('full-calender', [FullCalenderController::class, 'index'])->middleware(['auth']);
Route::post('full-calender/action', [FullCalenderController::class, 'action'])->middleware(['auth']);

Route::prefix('events')->group(function () {
    Route::get('index', [EventController::class, 'index'])->middleware(['auth'])->name('events');
    Route::get('add', [EventController::class, 'create'])->middleware(['auth'])->name('add_event');
    Route::post('add', [EventController::class, 'store'])->middleware(['auth']);
    Route::get('edit/{id}', [EventController::class, 'edit'])->middleware(['auth']);
    Route::post('edit/{id}', [EventController::class, 'update'])->middleware(['auth']);
    Route::get('edit/{id}/delete', [EventController::class, 'destroy'])->middleware(['auth']);
});
require __DIR__.'/auth.php';