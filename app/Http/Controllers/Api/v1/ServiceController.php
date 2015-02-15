<?php namespace App\Http\Controllers\Api\v1;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use \Response;
use \Input;
use \Validator;

use App\Models\Service;
use App\Models\User;
use App\Models\Phone;
use App\Models\Subscription;

class ServiceController extends Controller {

	public $token;
	public $service;
	public $user;

	function __construct(Service $service, Request $request, User $user, Phone $phone, Subscription $subscription){
		$this->token = $request->header('X-AUTH-TOKEN');
		$this->service = $service;
		$this->user = $user;
		$this->phone = $phone;
		$this->subscription = $subscription;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$services = Service::all();
		return Response::json(['success' => true, 'data' => $services], 200);
	}

	/**
	 * Checks if authenticated user is active
	 * within a specific service type
	 */
	public function isActive()
	{
		$user = $this->user->where('api_token', '=', $this->token)->first();
		$attrs = Input::get('data');
		$rules = $this->service->activeRules;
		$validator = Validator::make($attrs, $rules);
		if($validator->fails()){
			$errors = $validator->messages();
			return Response::json(['success' => false, 'errors' => $errors], 400);
		}
		//after validation success....
		//Check if phone number exists...
		$phone = $this->phone->where('phone_number', '=', $attrs['phone_number'])->where('user_id', '=', $user->id)->first();
		
		if(!$phone){
			return Response::json(['success' => false, 'errors' => ['This phone number does not exist or does not belong to you.'] ], 400);
		}

		//if phone number exists, lets check if we have a valid subscription....
		//lets check if the user is subscribed to this service
		$subscriptionService = $this->subscription
		->where('user_id', '=', $user->id)
		->where('service_id', '=', $attrs['service_id'])
		->where('phone_id', '=', $phone->id)
		->first();
		
		if(!$subscriptionService){
			return Response::json(['success' => false, 'errors' => ['You are NOT subscribed to this service with this phone number.'] ], 400);
		}

		$subscription = $this->subscription
		->where('user_id', '=', $user->id)
		->where('service_id', '=', $attrs['service_id'])
		->where('phone_id', '=', $phone->id)
		->where('link', '=', $attrs['link'])
		->first();

		if(!$subscription){
			return Response::json(['success' => false, 'errors' => ['You ARE subscribed to this service with this phone number but NOT with this link.'] ], 400);
		}

		//check if stripe plan is active 
		if(!$subscription->stripe_active){
			//subscription was cancelled.
			//lets show active until the end date of the subscription
			$today = strtotime(date('Y-m-d H:i:s'));
			$subscriptionEnds = strtotime($subscription->subscription_ends_at);
			if($subscriptionEnds <= $today){
				$active = false;
			}else{
				$active = true;
			}
		}else{
			$active = true;
		}

		return Response::json(['success' => true, 'active' => $active, 'data' => $subscription], 200);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
