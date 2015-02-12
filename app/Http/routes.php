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

	//List all services provided by text monster
	Route::get('services', 'ServiceController@index');
	
	//lets check if the service is active for a user...
	Route::post('service/active', 'SubscriptionController@isActive');

	//Handles the IPN for stripe. (called webhooks in stripe)
	Route::post('service/stripe', 'Laravel\Cashier\WebhookController@handleWebhook');

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