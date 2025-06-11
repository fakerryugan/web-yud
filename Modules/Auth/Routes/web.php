<?php

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
use Illuminate\Support\Facades\Route;
use Modules\Auth\Http\Controllers\DocumentController;
Route::get('/dokumen', function () {
    $dokumens = Dokumen::all(); 
    return view('dokumen.index', compact('dokumens'));
})->name('dokumen.index');
Route::get('/documents/view/{payload}', [DocumentController::class, 'viewFromPayload']);
Route::prefix('auth')->group(function() {
    Route::get('/', 'AuthController@index');
});
