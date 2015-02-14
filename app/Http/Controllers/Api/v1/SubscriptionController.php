<?php namespace App\Http\Controllers\Api\v1;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use \Response;
use \Input;
use \Validator;
use \Mail;

use App\Models\Subscription;
use App\Models\User;
use App\Models\Service;
use App\Models\Phone;
use App\Models\Order;

class SubscriptionController extends Controller {

	public $subscription;

	function __construct(Subscription $subscription, Order $order, User $user){
		$this->subscription = $subscription;
		$this->order = $order;
		$this->user = $user;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$subscriptions = Subscription::all();
		return Response::json(['success' => true, 'data' => $subscriptions], 200);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create($userId, $serviceId, $phoneId)
	{
		$service = Service::findOrFail($serviceId);
		return view('stripe.stripedemo')->with('service', $service);
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store($userId, $serviceId, $phoneId)
	{
		$stripe = Input::all();	
		$user = User::findOrFail($userId);
		$phone = Phone::findOrFail($phoneId);

		//Lets manually set our foreign key constraints
		$subscription = new $this->subscription;
		$subscription->user_id = $userId;
		$subscription->service_id = $serviceId;
		$subscription->stripe_plan = $serviceId;
		$subscription->phone_id = $phoneId;
		//lets set the subscription "active" field to 1.
		$subscription->active = 1;
		$subscription->save();

		$subscription->subscription($serviceId)->create($stripe['stripeToken']);
	
		return view('stripe.stripethankyou')->with('subscription', $subscription);
		//return Response::json(['success' => true, 'stripeData' => $stripe, 'data' => $subscription ], 220);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($userId, $subscriptionId)
	{
		$subscription = $this->subscription;
		$subscription = $subscription->findOrFail($subscriptionId);

		return Response::json(['success' => true, 'data' => $subscription], 200);
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
	 * Check if user has subscription to service
	 */
	public function isActive()
	{
		$attr = Input::get('data');
		$rules = [
			'user_id' => 'required',
			'service_id' => 'required',
			'phone_number' => 'required',
			'link' => 'required'
		];

		$validator = Validator::make($attr, $rules);

		if($validator->fails()){
			$errors = $validator->messages();
			return Response::json(['success' => false, 'errors' => $errors], 400);
		}
		
		//If we pass validation....
		$phone = Phone::where('phone_number', '=', $attr['phone_number'])->first();
		
		if(!$phone){
			return Response::json(['success' => false, 'errors' => ['This phone number does not exist.'] ], 400);
		}

		//lets check if the user is subscribed to this service
		$subscriptionService = $this->subscription
		->where('user_id', '=', $attr['user_id'])
		->where('service_id', '=', $attr['service_id'])
		->where('phone_id', '=', $phone->id)
		->first();

		if(!$subscriptionService){
			return Response::json(['success' => false, 'errors' => ['You are NOT subscribed to this service with this phone number.'] ], 400);
		}


		$subscription = $this->subscription
		->where('user_id', '=', $attr['user_id'])
		->where('service_id', '=', $attr['service_id'])
		->where('phone_id', '=', $phone->id)
		->where('link', '=', $attr['link'])
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
	public function destroy($userId, $subscriptionId)
	{
		//cancel subscription
		$user = User::findOrFail($userId);
		$subscription = $this->subscription->findOrFail($subscriptionId);
		$subscription->subscription()->cancel();
		
		return Response::json([
			'success' => true, 
			'msg' => 'the subscription has been canceled'
			], 200);
	}

	/**
	 * Handles the IPN from Stripe. (AKA Web Hooks)
	 * On stripe.
	 */
	public function transaction()
	{	
		$payload = Input::all();
		$subscription = []; //default

		if($payload['type'] == "invoice.payment_failed")
		{
			//invoice failed...
			if($payload['data']['object']['attempt_count'] > 3){
				//the recurring payment has failed more than 3 times!
				$stripeSubscriptionId = $payload['data']['object']['subscription'];
				$subscription = $this->subscription->where('stripe_subscription', '=', $stripeSubscriptionId)->first();
				
				if($subscription){
					//cancel the subscription after 3 failed payments!
					$subscription->subscription()->cancel();
				}
			}
		}
		
		//Lets handle the end of a subscription...
		if($payload['type'] == "customer.subscription.deleted")
		{
			$subscription = $this->subscription->where('stripe_subscription', '=', $payload['data']['object']['id'])->first();
			$subscription->active = 0;
			$subscription->save();
			
			//lets do a soft delete....
			$subscriptionId = $subscription->id;
			$subscription->delete($subscriptionId);
			
		}


		return Response::json(['success' => true, 'subscription' => $subscription, 'dataPosted' => $payload], 200);
	}
}
