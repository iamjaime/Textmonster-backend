<?php namespace App\Http\Controllers\Api\v1;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use \Response;
use \Input;
use \Validator;

use App\Models\User;
use App\Models\Friend;
use App\Models\FriendRequest;

class FriendController extends Controller {

	public $token;
	public $service;
	public $user;

	function __construct(Request $request, User $user, Friend $friend, FriendRequest $friendRequest){
		
		$this->token = $request->header('X-AUTH-TOKEN');
		$this->user = $user;
		$this->friend = $friend;
		$this->friendRequest = $friendRequest;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = $this->user->where('api_token', '=', $this->token)->first();
		$friends = $this->friend->getFriendInfo($user->id);

		return Response::json(['success' => true, 'data' => $friends], 200);
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
	public function destroy()
	{
		$user = $this->user->where('api_token', '=', $this->token)->first();
		$attrs = Input::get('data');
		$rules = $this->friend->deleteFriendRules;
		$validator = Validator::make($attrs, $rules);
		if($validator->fails()){
			$errors = $validator->messages();
			return Response::json(['success' => false, 'errors' => $errors], 400);
		}
		//after validation success...
		$friend = $this->friend->where('user_id', '=', $user->id)
		->where('friend_id', '=', $attrs['friend_id'])->first();
		$friend->delete();

		return Response::json(['success' => true ], 200);
	}

	/**
	 * List All friend requests for authenticated user.
	 */
	public function friendRequest()
	{
		$user = $this->user->where('api_token', '=', $this->token)->first();
		$friends = $this->friendRequest->where('requested_user_id', '=', $user->id)->get();

		return Response::json(['success' => true, 'data' => $friends ], 200);
	}

	/**
	 * Accept Friend Request
	 */
	public function acceptFriendRequest()
	{
		$attrs = Input::get('data');
		$user = $this->user->where('api_token', '=', $this->token)->first();
		$rules = $this->friendRequest->acceptFriendRules;

		$validator = Validator::make($attrs, $rules);
		if($validator->fails()){
			$errors = $validator->messages();
			return Response::json(['success' => false, 'errors' => $errors], 400);
		}

		$friends = $this->friendRequest->findOrFail($attrs['request_id']);
		$friend_id = $friends->requested_user_id;

		//after validation success...
		$friend = new $this->friend;
		$friend->user_id = $user->id;
		$friend->friend_id = $friend_id;
		$friend->save();

		//Now lets delete the friend request...
		$friends->delete($attrs['request_id']);

		return Response::json(['success' => true, 'message' => 'Friend Successfully Added.'], 200);
	}

	/**
	 * Make Friend Request
	 */
	public function makeFriendRequest()
	{
		$attrs = Input::get('data');
		$user = $this->user->where('api_token', '=', $this->token)->first();
		$rules = $this->friendRequest->makeFriendRules;

		$validator = Validator::make($attrs, $rules);
		if($validator->fails()){
			$errors = $validator->messages();
			return Response::json(['success' => false, 'errors' => $errors], 400);
		}

		//now lets check if a friend request has already been sent.
		$pendingRequest = $this->friendRequest
		->where('requesting_user_id', '=', $user->id)
		->where('requested_user_id', '=', $attrs['requested_user_id'])
		->first();

		if($pendingRequest){
			return Response::json(['success' => false, 'errors' => 'A friend request has already been sent and is pending approval.'], 400);
		}

		//now lets check if we are already friends with this user....
		$currentlyFriends = $this->friend->where('user_id', '=', $user->id)->where('friend_id', '=', $attrs['requested_user_id'])->first();

		if($currentlyFriends){
			return Response::json(['success' => false, 'errors' => 'You are already friends with the requested user.'], 400);
		}

		$friendRequest = new $this->friendRequest;
		$friendRequest->requesting_user_id = $user->id;
		$friendRequest->requested_user_id = $attrs['requested_user_id'];
		$friendRequest->save();

		$friend = $this->user->findOrFail($attrs['requested_user_id']);

		return Response::json(['success' => true, 'request_created' => true, 'request_id' => $friendRequest->id, 'data' => $friend], 200);
	}

	/**
	 * Decline Friend Request
	 */
	public function declineFriendRequest()
	{
		$attrs = Input::get('data');
		$user = $this->user->where('api_token', '=', $this->token)->first();
		$rules = $this->friendRequest->declineFriendRules;

		$validator = Validator::make($attrs, $rules);
		if($validator->fails()){
			$errors = $validator->messages();
			return Response::json(['success' => false, 'errors' => $errors], 400);
		}

		$friends = $this->friendRequest->findOrFail($attrs['request_id']);
		
		//Now lets delete the friend request...
		$friends->delete($attrs['request_id']);

		return Response::json(['success' => true, 'message' => 'You have declined the friend request.'], 200);	
	}
}
