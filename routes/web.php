<?php

use App\Http\Controllers\CTConnect;
use App\Http\Controllers\DDTechController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PreciosController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\MiPCController;
use App\Http\Controllers\ZegucomController;
use App\Http\Controllers\ScraperController;
use App\Http\Controllers\GrupoDecmeController;
use App\Http\Controllers\ImagenesController;
use App\Http\Controllers\MargenesController;
use App\Http\Controllers\PalabrasClaveController;
use App\Http\Controllers\ProductosController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PromocionesController;
use App\Http\Controllers\TopsController;
use App\Models\Margenes;
// use Google\Service\Analytics;
// use Analytics;
use Spatie\Analytics\AnalyticsFacade as Analytics;
use Spatie\Analytics\Period;

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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/importar', function () {
//     return view('importar');
// });

// Route::get('/scraping', [ScraperController::class, 'index']);
Route::get('/importar', [ExcelController::class, 'index']);
// Route::post('/import', [ExcelController::class, 'importData']);
Route::get('/export', [ExcelController::class, 'exportData']);

Route::get('/precios', [PreciosController::class, 'index']);
Route::get('/preciosmipc', [MiPCController::class, 'index']);
Route::get('/precioscp', [ScraperController::class, 'index']);
Route::post('/import', [PreciosController::class, 'cotizar']);
Route::post('/importMiPC', [MiPCController::class, 'cotizar']);
Route::post('/importCyberpuerta', [ScraperController::class, 'cotizar']);
Route::get('/precioszegucom', [ZegucomController::class, 'index']);
Route::post('/importZegucom', [ZegucomController::class, 'cotizar']);
Route::get('/preciosgrupodecme', [GrupoDecmeController::class, 'index']);
Route::get('/productos', [ProductosController::class, 'index']);
Route::post('/productos', [ProductosController::class, 'index']);
// Route::post('/importGrupoDecme', [GrupoDecmeController::class, 'cotizar']);
Route::post('/importGrupoDecme', [GrupoDecmeController::class, 'llenadoJSON']);
// Route::get('/precios', 'PreciosController@index');
Route::get('/preciosddtech', [DDTechController::class, 'index']);
Route::post('/importDDTech', [DDTechController::class, 'cotizar']);

Route::get('get/{id?}', [PreciosController::class,'getCategorias'])->name('getCategorias');
Route::get('getMarca/{id?}/{id2?}', [PreciosController::class,'getMarcas'])->name('getMarcas');
// Route::get('books/{id}',['as'=>'books.view','uses'=>'BOOKController@view']);
Route::get("json", [PreciosController::class, "lectura"]);
Route::get("jsonLocal", [PreciosController::class, "lecturaLocal"]);
Route::get("token", [CTConnect::class, "token"]);

// MÁRGENES //
Route::get("/margenes", [MargenesController::class, "index"]);

// EXISTENCIAS //
Route::get('/existencias', [ProductosController::class, 'existencias']);

// PALABRAS CLAVE //
Route::get('/palabras-clave/nuevas', [PalabrasClaveController::class, 'nuevas']);

//GOOGLE MY BUSINESS
Route::get('/productos/gmb', [ProductosController::class, 'google_my_business']);
Route::get('/productos/gmb-nuevos', [ProductosController::class, 'nuevos_gmb']);

//IMAGENES NUEVAS//
Route::get('productos/imagen', [ProductosController::class, 'imagen']);

//CRAWLER IMAGENES//
Route::get('imagenes', [ImagenesController::class, 'obtener']);
Route::get('ejemploImagenes', [ImagenesController::class, 'ejemploImagenes']);
Route::get('obtener/{filename}', [ImagenesController::class, 'getFile'])->name('getfile');


//CARTAS PRODUCTOS
Route::get('/productos/cartas',[ProductosController::class, 'cartas']);
Route::post('/productos/cartas',[ProductosController::class, 'cartas']);

//CARTAS MARGENES
Route::get('Margenes/Mayor', [MargenesController::class, 'cartaMayor']);
Route::get('Margenes/Menor',[MargenesController::class, 'cartaMenor']);
// PROMOCIONES //
Route::get('promociones/nuevas', [PromocionesController::class, 'nuevas']);
Route::get('promociones/vigentes', [PromocionesController::class, 'vigentes']);
Route::get('promociones/mes', [PromocionesController::class, 'delMes']);
Route::get('promociones', [PromocionesController::class, 'index']);
Route::get('promociones/vencidas', [PromocionesController::class, 'vencidas']);
Route::get('/productos/gmb', [ProductosController::class, 'google_my_business']);

//CARTA PROMOCIONES
Route::get('Promociones/Cartas', [PromocionesController::class, 'cartaPromociones']);

Route::post('projects/importProject', [ProjectController::class, 'importProject'])->name('importProject');
Route::resource('projects', ProjectController::class);

Route::post('tops/importTops', [TopsController::class, 'importTops'])->name('importTops');
Route::post('tops', [TopsController::class, 'index'])->name('consultarTops');
Route::get('tops', [TopsController::class, 'index'])->name('top100');
// Route::get('tops/importTops', [TopsController::class, 'importTops'])->name('importTops');
// Route::resource('tops', TopsController::class);
Route::get('/table-datatable', function () {
    return view('table-datatable');
});

Route::get('/analytics', function () {
    // $prueba = Analytics::fetchTopBrowsers(Period::days(7));
    // dd($prueba);
    $analyticsData = Analytics::fetchVisitorsAndPageViews(Period::days(7));
    return view('ga', ['analyticsData' => $analyticsData]);
}); 


Route::get('subir-tops', function () {
    return view('file-upload');
});


Route::get('/', function () {
    return view('index-vertical');
});
Route::get('/index', function () {
    return view('index-vertical');
});
Route::get('/dashboard-alternate', function () {
    return view('dashboard-alternate');
});
/*App*/
Route::get('/app-emailbox', function () {
    return view('app-emailbox');
});
Route::get('/app-emailread', function () {
    return view('app-emailread');
});
Route::get('/app-chat-box', function () {
    return view('app-chat-box');
});
Route::get('/app-file-manager', function () {
    return view('app-file-manager');
});
Route::get('/app-contact-list', function () {
    return view('app-contact-list');
});
Route::get('/app-to-do', function () {
    return view('app-to-do');
});
Route::get('/app-invoice', function () {
    return view('app-invoice');
});
Route::get('/app-fullcalender', function () {
    return view('app-fullcalender');
});
/*Charts*/
Route::get('/charts-apex-chart', function () {
    return view('charts-apex-chart');
});
Route::get('/charts-chartjs', function () {
    return view('charts-chartjs');
});
Route::get('/charts-highcharts', function () {
    return view('charts-highcharts');
});
/*ecommerce*/
Route::get('/ecommerce-products', function () {
    return view('ecommerce-products');
});
Route::get('/ecommerce-products-details', function () {
    return view('ecommerce-products-details');
});
Route::get('/ecommerce-add-new-products', function () {
    return view('ecommerce-add-new-products');
});
Route::get('/ecommerce-orders', function () {
    return view('ecommerce-orders');
});

/*Components*/
Route::get('/widgets', function () {
    return view('widgets');
});
Route::get('/component-alerts', function () {
    return view('component-alerts');
});
Route::get('/component-accordions', function () {
    return view('component-accordions');
});
Route::get('/component-badges', function () {
    return view('component-badges');
});
Route::get('/component-buttons', function () {
    return view('component-buttons');
});
Route::get('/component-cards', function () {
    return view('component-cards');
});
Route::get('/component-carousels', function () {
    return view('component-carousels');
});
Route::get('/component-list-groups', function () {
    return view('component-list-groups');
});
Route::get('/component-media-object', function () {
    return view('component-media-object');
});
Route::get('/component-modals', function () {
    return view('component-modals');
});
Route::get('/component-navs-tabs', function () {
    return view('component-navs-tabs');
});
Route::get('/component-navbar', function () {
    return view('component-navbar');
});
Route::get('/component-paginations', function () {
    return view('component-paginations');
});
Route::get('/component-popovers-tooltips', function () {
    return view('component-popovers-tooltips');
});
Route::get('/component-progress-bars', function () {
    return view('component-progress-bars');
});
Route::get('/component-spinners', function () {
    return view('component-spinners');
});
Route::get('/component-notifications', function () {
    return view('component-notifications');
});
Route::get('/component-avtars-chips', function () {
    return view('component-avtars-chips');
});
/*Content*/
Route::get('/content-grid-system', function () {
    return view('content-grid-system');
});
Route::get('/content-typography', function () {
    return view('content-typography');
});
Route::get('/content-text-utilities', function () {
    return view('content-text-utilities');
});
/*Icons*/
Route::get('/icons-line-icons', function () {
    return view('icons-line-icons');
});
Route::get('/icons-boxicons', function () {
    return view('icons-boxicons');
});
Route::get('/icons-feather-icons', function () {
    return view('icons-feather-icons');
});
/*Authentication*/
Route::get('/authentication-signin', function () {
    return view('authentication-signin');
});
Route::get('/authentication-signup', function () {
    return view('authentication-signup');
});
Route::get('/authentication-signin-with-header-footer', function () {
    return view('authentication-signin-with-header-footer');
});
Route::get('/authentication-signup-with-header-footer', function () {
    return view('authentication-signup-with-header-footer');
});
Route::get('/authentication-forgot-password', function () {
    return view('authentication-forgot-password');
});
Route::get('/authentication-reset-password', function () {
    return view('authentication-reset-password');
});
Route::get('/authentication-lock-screen', function () {
    return view('authentication-lock-screen');
});
/*Table*/
Route::get('/table-basic-table', function () {
    return view('table-basic-table');
});
Route::get('/table-datatable', function () {
    return view('table-datatable');
});
/*Pages*/
Route::get('/user-profile', function () {
    return view('user-profile');
});
Route::get('/timeline', function () {
    return view('timeline');
});
Route::get('/pricing-table', function () {
    return view('pricing-table');
});
Route::get('/errors-404-error', function () {
    return view('errors-404-error');
});
Route::get('/errors-500-error', function () {
    return view('errors-500-error');
});
Route::get('/errors-coming-soon', function () {
    return view('errors-coming-soon');
});
Route::get('/error-blank-page', function () {
    return view('error-blank-page');
});
Route::get('/faq', function () {
    return view('faq');
});
/*Forms*/
Route::get('/form-elements', function () {
    return view('form-elements');
});

Route::get('/form-input-group', function () {
    return view('form-input-group');
});
Route::get('/form-layouts', function () {
    return view('form-layouts');
});
Route::get('/form-validations', function () {
    return view('form-validations');
});
Route::get('/form-wizard', function () {
    return view('form-wizard');
});
Route::get('/form-text-editor', function () {
    return view('form-text-editor');
});
Route::get('/form-file-upload', function () {
    return view('form-file-upload');
});
Route::get('/form-date-time-pickes', function () {
    return view('form-date-time-pickes');
});
Route::get('/form-select2', function () {
    return view('form-select2');
});
/*Maps*/
Route::get('/map-google-maps', function () {
    return view('map-google-maps');
});
Route::get('/map-vector-maps', function () {
    return view('map-vector-maps');
});
/*Un-found*/
Route::get('/test/content-grid-system', function () {
    return view('test/content-grid-system');
});
