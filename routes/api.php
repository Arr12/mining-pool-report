<?php

use App\Http\Controllers\SheetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('/spreadsheet')->name('api.spreadsheet')->group(function(){
    Route::get('/get-worksheet', [SheetController::class, 'GetWorksheet'])->name('get-worksheet');
    Route::get('/get-value', [SheetController::class, 'GetValue'])->name('get-value');
    Route::get('/get-all', [SheetController::class, 'GetAll'])->name('get-all');
    Route::get('/get-ss-data', [SheetController::class, 'GetSSData'])->name('get-ss-data');
});
