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

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::get('full-calender', [FullCalenderController::class, 'index'])->middleware(['auth']);
Route::post('full-calender/action', [FullCalenderController::class, 'action'])->middleware(['auth']);

Route::prefix('events')->group(function () {
    Route::get('index', [EventController::class, 'index'])->middleware(['auth']);
    Route::get('edit/{id}', [EventController::class, 'edit'])->middleware(['auth']);
});
require __DIR__.'/auth.php';