<?php

use Illuminate\Support\Facades\Route;
use App\Jobs\SyncUsersJob;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/', 'App\Http\Controllers\AuthController@login')->name('login');
Route::get('/login', 'App\Http\Controllers\AuthController@login')->name('login');
Route::post('/loginProses', 'App\Http\Controllers\AuthController@loginProses');


//BACKEND
Route::group(['middleware' => 'auth'], function () {


    //DASHBOARD
    Route::get('/dashboard', 'App\Http\Controllers\DashboardController@index');

    //USER
    Route::get('/user', 'App\Http\Controllers\UserController@index');
    Route::get('/data-user', 'App\Http\Controllers\UserController@data');
    Route::post('/store-user', 'App\Http\Controllers\UserController@store');
    Route::post('/update-user', 'App\Http\Controllers\UserController@update');
    Route::post('/delete-user', 'App\Http\Controllers\UserController@delete');


    //DETAIL USER
    Route::get('/detail-user', 'App\Http\Controllers\DetailUserController@index');
    Route::post('/store-detail-user', 'App\Http\Controllers\DetailUserController@store');
   
    //JABATAN FUNGSIONAL
    Route::get('/jabatan-fungsional', 'App\Http\Controllers\JabatanFungsionalController@index');
    Route::get('/data-jabatan-fungsional', 'App\Http\Controllers\JabatanFungsionalController@data');
    Route::post('/store-jabatan-fungsional', 'App\Http\Controllers\JabatanFungsionalController@store');
    Route::post('/update-jabatan-fungsional', 'App\Http\Controllers\JabatanFungsionalController@update');
    Route::post('/delete-jabatan-fungsional', 'App\Http\Controllers\JabatanFungsionalController@delete');


    //KEPANGKATAN
    Route::get('/kepangkatan', 'App\Http\Controllers\KepangkatanController@index');
    Route::get('/data-kepangkatan', 'App\Http\Controllers\KepangkatanController@data');
    Route::post('/store-kepangkatan', 'App\Http\Controllers\KepangkatanController@store');
    Route::post('/update-kepangkatan', 'App\Http\Controllers\KepangkatanController@update');
    Route::post('/delete-kepangkatan', 'App\Http\Controllers\KepangkatanController@delete');

    //ANGKA KREDIT
    Route::get('/angka-kredit', 'App\Http\Controllers\AngkaKreditController@index');
    Route::get('/data-angka-kredit', 'App\Http\Controllers\AngkaKreditController@data');
    Route::post('/store-angka-kredit', 'App\Http\Controllers\AngkaKreditController@store');
    Route::post('/update-angka-kredit', 'App\Http\Controllers\AngkaKreditController@update');
    Route::post('/delete-angka-kredit', 'App\Http\Controllers\AngkaKreditController@delete');


    //PENDIDIKAN
    Route::get('/pendidikan', 'App\Http\Controllers\PendidikanController@index');
    Route::get('/data-pendidikan', 'App\Http\Controllers\PendidikanController@data');
    Route::post('/store-pendidikan', 'App\Http\Controllers\PendidikanController@store');
    Route::post('/update-pendidikan', 'App\Http\Controllers\PendidikanController@update');
    Route::post('/delete-pendidikan', 'App\Http\Controllers\PendidikanController@delete');


    //DIKLAT
    Route::get('/diklat', 'App\Http\Controllers\DiklatController@index');
    Route::get('/data-diklat', 'App\Http\Controllers\DiklatController@data');
    Route::post('/store-diklat', 'App\Http\Controllers\DiklatController@store');
    Route::post('/update-diklat', 'App\Http\Controllers\DiklatController@update');
    Route::post('/delete-diklat', 'App\Http\Controllers\DiklatController@delete');
});





