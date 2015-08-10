<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::group(['domain'=>'{domain}.lapinedaback.app'],function(){
	Route::any('/',function($domain){
		echo $domain;
		switch ($domain) {
			case 'auth':
				return Redirect::route('auth');
				break;
			case 'admin':
				return Redirect::route('admin');
			default:
				return Redirect::to('auth');
				break;
		}
	});
});
Route::group(['prefix'=>'api/auth/','as'=>'auth','middleware' => 'cors'],function(){
	Route::get('csrf_token','Auth\AuthController@token');
	Route::post('login','Auth\AuthController@login');
	Route::post('register','Auth\AuthController@create');
	Route::get('register/verify/{id}/{token}','Auth\AuthController@verify_account');
	Route::post('register/verify/resend_activation_email','Auth\AuthController@resend_activation_email');
	Route::get('logout',"Auth\AuthController@getLogout");
	Route::post("password/email","Auth\PasswordController@postEmail");
	Route::get("password/reset/{token}","Auth\PasswordController@getReset");
	Route::post("password/reset","Auth\PasswordController@postReset");
});

Route::group(['preix'=>'api/admin','as'=>'admin','middleware'=>['cors','auth']],function(){
	Route::get('dashboard','Admin/AdminController@dahboard');
});