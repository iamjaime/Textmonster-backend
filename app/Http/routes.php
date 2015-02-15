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

/**
 * Text Monster API Version 1.0.0
 */

/**
 * Default Namespace for all controllers
 */
Route::group(['namespace' => 'App\Http\Controllers'], function(){

	/**
	 * Unauthenticated Resources in the API
	 */
	Route::group(['prefix' => 'api/v1', 'namespace' => 'Api\v1'], function(){

		//Login Users Unauthenticated route 
		Route::post('users/login', 'UserController@authenticate');

		//Signup Users Unauthenticated route
		Route::post('users/signup', 'UserController@store');

		//Handles the IPN for stripe. (called webhooks in stripe)
		Route::post('services/stripe', 'SubscriptionController@transaction');

	});


	/**
	 * Authenticated Resources in the API
	 */
	Route::group(['prefix' => 'api/v1', 'namespace' => 'Api\v1', 'middleware' => 'auth.token'], function() { 

		//List all services provided by text monster for this specific user
		Route::get('services', 'ServiceController@index');
		
		//lets check if the service is active for this specific user...
		Route::post('services/active', 'SubscriptionController@isActive');

		//List all targets and allow target searching....
		Route::get('targets', 'TargetController@index');


		Route::resource('users', 'UserController');
		Route::post('users/{id}/restore', 'UserController@restore');

		Route::resource('users.phones', 'PhoneController');
		Route::resource('users.subscriptions', 'SubscriptionController');

		//Handle subscriptions
		Route::get('users/{userId}/service/{serviceId}/phone/{phoneId}/subscribe', 'SubscriptionController@create');
		Route::post('users/{userId}/service/{serviceId}/phone/{phoneId}/subscribe', 'SubscriptionController@store');



		Route::resource('users.orders', 'OrderController');
		Route::resource('users.messages', 'MessageController');


	});

});