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
    Route::get('/', [PageController::class, 'IndexHome']);
    Route::get('/mining', [PageController::class, 'IndexValues'])->name('mining');
    Route::get('/mining-data', [PageController::class, 'GetValues'])->name('mining-data');
    Route::get('/withdraw-data', [PageController::class, 'GetValuesWithdraw'])->name('withdraw-data');
    Route::prefix('/master')->name('master.')->group(function () {
        Route::get('/user', [PageController::class, 'IndexMasterUser'])->name('user');
        Route::get('/user-data', [PageController::class, 'GetMasterUser'])->name('user-data');
        Route::put('/user-put', [PageController::class, 'PutMasterUser'])->name('user-put');
        Route::post('/user-add', [PageController::class, 'AddMasterUser'])->name('user-add');
    });
});

// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//     return view('dashboard');
// })->name('dashboard');
