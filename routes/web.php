<?php

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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([ 'ns.installed' ])->group( function() {

    Route::get( '/sign-in', 'AuthController@signIn' )->name( 'login' );
    Route::get( '/sign-up', 'AuthController@signUp' );
    Route::get( '/password-lost', 'AuthController@passwordLost' );
    Route::get( '/new-password', 'AuthController@newPassword' );

    Route::post( '/auth/sign-in', 'AuthController@postSignIn' );

    Route::middleware([ 'auth' ])->group( function() {
        Route::get( '/dashboard', 'DashboardController@home' )->name( 'dashboard.index' );
        Route::get( '/dashboard/customers', 'Dashboard\CustomersController@listCustomers' );
    });
});

Route::middleware([ 'ns.not-installed' ])->group( function() {
    Route::prefix( '/do-setup/' )->group( function() {
        Route::get( '', 'SetupController@welcome' )->name( 'setup' );
    });
});

Route::get( '/routes', function() {
    return ( array ) app( 'router' )->getRoutes();
});