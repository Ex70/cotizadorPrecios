<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PreciosController;
use App\Http\Controllers\ExcelController;

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

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/importar', function () {
//     return view('importar');
// });

Route::get('/importar', [ExcelController::class, 'index']);
Route::post('/import', [ExcelController::class, 'importData']);
Route::get('/export', [ExcelController::class, 'exportData']);

Route::get('/precios', [PreciosController::class, 'index']);
// Route::get('/precios', 'PreciosController@index');