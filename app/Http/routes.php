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
 * Laravel Cashier (stripe) webhook controller
 */
Route::group(['namespace' => 'Laravel\Cashier'], function(){
	//Handles the IPN for stripe. (called webhooks in stripe)
	//Route::post('service/stripe', 'WebhookController@handleWebhook');
});

/**
 * Default Namespace for all controllers
 */
Route::group(['namespace' => 'App\Http\Controllers'], function(){

Route::group(['prefix' => 'api/v1', 'namespace' => 'Api\v1'], function(){

	//Login Users Unauthenticated route 
	Route::post('users/login', 'UserController@authenticate');

	//Signup Users Unauthenticated route
	Route::post('users/signup', 'UserController@store');

});


	/**
	 * Text Monster API Version 1.0.0
	 * Authenticated Resources
	 */
	Route::group(['prefix' => 'api/v1', 'namespace' => 'Api\v1', 'middleware' => 'auth.token'], function() { 

		//List all services provided by text monster
		Route::get('services', 'ServiceController@index');
		
		//lets check if the service is active for a user...
		Route::post('service/active', 'SubscriptionController@isActive');

		//Handles the IPN for stripe. (called webhooks in stripe)
		Route::post('service/stripe', 'SubscriptionController@transaction');

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