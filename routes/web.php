<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PreciosController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\MiPCController;
use App\Http\Controllers\ScraperController;

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

Route::get('/scraping', [ScraperController::class, 'index']);
Route::get('/importar', [ExcelController::class, 'index']);
// Route::post('/import', [ExcelController::class, 'importData']);
Route::get('/export', [ExcelController::class, 'exportData']);

Route::get('/precios', [PreciosController::class, 'index']);
Route::get('/preciosmipc', [MiPCController::class, 'index']);
Route::post('/import', [PreciosController::class, 'cotizar']);
Route::post('/importMiPC', [MiPCController::class, 'cotizar']);
// Route::get('/precios', 'PreciosController@index');

Route::get('get/{id?}', [PreciosController::class,'getCategorias'])->name('getCategorias');
// Route::get('books/{id}',['as'=>'books.view','uses'=>'BOOKController@view']);
