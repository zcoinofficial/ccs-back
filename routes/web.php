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

Route::get('/', function () {
    return view('welcome');
});
Route::get('projects', ['as' => 'ffs', 'uses' => FundingController::class.'@index']);
Route::get('projects/{paymentId}', ['as' => 'ffs', 'uses' => FundingController::class.'@show']);
Route::get('projects/{paymentId}/donate', ['as' => 'ffs', 'uses' => FundingController::class.'@donate']);
