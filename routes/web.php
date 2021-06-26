<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\SheetController;
use Illuminate\Support\Facades\Route;

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
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/', function () {
        return view('admin.pages.home');
    });
    Route::get('/mining', [PageController::class, 'IndexValues'])->name('mining');
    Route::get('/mining-data', [PageController::class, 'GetValues'])->name('mining-data');
    Route::get('/withdraw-data', [PageController::class, 'GetValuesWithdraw'])->name('withdraw-data');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');