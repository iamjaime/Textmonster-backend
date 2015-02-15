<?php namespace App\Http\Controllers\Api\v1;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use \Response;
use \Input;

use App\Models\User;
use App\Models\Order;


class OrderController extends Controller {

	public $order;
	public $user;
	public $token;

	function __construct(User $user, Order $order, Request $request){
		$this->token = $request->header('X-AUTH-TOKEN');
		$this->user = $user;
		$this->order = $order;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = $this->user->where('api_token', '=', $this->token)->with('orders')->first();
		$order = $this->order->where('user_id', '=', $user->id)->get();
		return Response::json(['success' => true, 'data' => $order], 200);
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
	public function show($orderId)
	{
		$user = $this->user->where('api_token', '=', $this->token)->first();
		$order = $this->order->where('user_id', '=', $user->id)->where('id', '=', $orderId)->first();
		if(!$order){
			return Response::json(['success' => false, 'errors' => 'The order id does not exist or does not belong to this user.'], 400);
		}
		return Response::json(['success' => true, 'data' => $order], 200);
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
