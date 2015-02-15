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

		//Restore a user that has been soft deleted.
		Route::post('users/{id}/restore', 'UserController@restore');

		//List all services provided by text monster for this specific user
		Route::get('services', 'ServiceController@index');

		//Handles the IPN for stripe. (called webhooks in stripe)
		Route::post('services/stripe', 'SubscriptionController@transaction');

	});


	/**
	 * Authenticated Resources in the API
	 */
	Route::group(['prefix' => 'api/v1', 'namespace' => 'Api\v1', 'middleware' => 'auth.token'], function() { 

		//lets check if the service is active for this specific user...
		Route::post('services/active', 'ServiceController@isActive');

		//List all targets and allow target searching....
		Route::get('targets', 'TargetController@index');

		//USERS
		Route::get('users', 'UserController@index');
		Route::put('users', 'UserController@update');
		Route::delete('users', 'UserController@destroy');
	
		//PHONES
		Route::get('phones', 'PhoneController@index');
		Route::post('phones', 'PhoneController@store');
		Route::put('phones/{phoneId}', 'PhoneController@update');
		Route::get('phones/{phoneId}', 'PhoneController@show');


		//Orders
		Route::get('orders', 'OrderController@index');
		Route::get('orders/{orderId}', 'OrderController@show');

		//Friends
		Route::get('friends', 'FriendController@index');
		Route::delete('friends', 'FriendController@destroy');
		Route::get('friends/requests', 'FriendController@friendRequest');

		Route::post('friends/requests/accept', 'FriendController@acceptFriendRequest');
		Route::post('friends/requests/make', 'FriendController@makeFriendRequest');
		Route::post('friends/requests/decline', 'FriendController@declineFriendRequest');


		Route::resource('users.subscriptions', 'SubscriptionController');

		//Handle subscriptions
		Route::get('users/{userId}/service/{serviceId}/phone/{phoneId}/subscribe', 'SubscriptionController@create');
		Route::post('users/{userId}/service/{serviceId}/phone/{phoneId}/subscribe', 'SubscriptionController@store');


		Route::resource('users.orders', 'OrderController');
		Route::resource('users.messages', 'MessageController');


	});

});