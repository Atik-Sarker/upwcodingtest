<?php

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
Route::group(['middleware' => 'api','namespace' => 'Api\Auth','prefix' => 'auth'], function ($router) {
    Route::post('login', 'AuthController@login')->name('login');
    Route::post('logout', 'AuthController@logout');
    Route::post('refresh', 'AuthController@refresh');
    Route::post('me', 'AuthController@me');
});
Route::namespace('Api')->group( function () {
    Route::middleware(['auth.jwt', 'auth:api'])->group(function (){
        Route::get('user-list', 'UserInvitationController@userList');
        Route::post('invite-user', 'UserInvitationController@inviteUser');
        Route::post('user-profile-update', 'UserInvitationController@userProfileUpdate');
    });
    Route::post('registration', 'UserInvitationController@userRegistration')->name('registration');
    Route::post('active', 'UserInvitationController@userActive');
});


