<?php namespace App\Http\Controllers\Api\v1;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use \Response;
use \Input;

use App\Models\Subscription;
use App\Models\User;
use App\Models\Service;
use App\Models\Phone;

class SubscriptionController extends Controller {

	public $subscription;

	function __construct(Subscription $subscription){
		$this->subscription = $subscription;
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
