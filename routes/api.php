<?php

use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\TransportController;
use App\Http\Controllers\Api\ContactusController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
 
//     Route::get('/faq', [FaqController::class, 'index'])
//         ->name('api.faq.index');

// Route::get('/page/{slug}', [FaqController::class, 'testing']);


Route::get('transport', [TransportController::class, 'index']);
Route::post('transport', [TransportController::class, 'formSubmission']);
Route::post('contactus', [ContactusController::class, 'formSubmission']);

});
