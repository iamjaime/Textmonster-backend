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

Route::get('/', 'WelcomeController@index');
Route::get('home', 'HomeController@index');


Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);


/**
 * Text Monster API Version 1.0.0
 */
Route::group(['prefix' => 'api/v1', 'namespace' => 'Api\v1'], function() { 

	Route::get('services', 'ServiceController@index');
	Route::get('targets', 'TargetController@index');

	Route::resource('users', 'UserController');
	Route::post('users/{id}/restore', 'UserController@restore');

	Route::resource('users.phones', 'PhoneController');
	
	Route::resource('users.subscriptions', 'SubscriptionController');

	Route::get('users/{userId}/subscriptions/{serviceId}/phone/{phoneId}/create', 'SubscriptionController@create');
	Route::post('users/{userId}/subscriptions/{serviceId}/phone/{phoneId}/create', 'SubscriptionController@store');

	Route::resource('users.orders', 'OrderController');
	Route::resource('users.messages', 'MessageController');


});