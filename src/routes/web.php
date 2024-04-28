<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimestampController;

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

Route::middleware('auth')->group(function () {
    Route::get('/', [TimestampController::class, 'index']);
    Route::get('/attendance/', [TimestampController::class, 'attendance']);
    Route::get('/attendance/{date}', [TimestampController::class, 'attendance']);
    Route::post('/work-start', [TimestampController::class, 'workStart']);
    Route::post('/work-end', [TimestampController::class, 'workEnd']);
    Route::post('/break-start', [TimestampController::class, 'breakStart']);
    Route::post('/break-end', [TimestampController::class, 'breakEnd']);
});



