<?php namespace App\Http\Controllers\Api\v1;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use \Response;
use \Input;
use \Validator;

use App\Models\User;
use App\Models\Phone;

class PhoneController extends Controller {
	
	public $user;
	public $phone;
	public $token;

	function __construct(User $user, Phone $phone, Request $request){
		$this->token = $request->header('X-AUTH-TOKEN');
		$this->user = $user;
		$this->phone = $phone;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = $this->user->where('api_token', '=', $this->token)->first();
		$phones = $this->phone->where('user_id', '=', $user->id)->get();
		return Response::json(['success' => true, 'data' => $phones], 200);
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
		$user = $this->user->where('api_token', '=', $this->token)->first();
		$attr = Input::get('data');
		$rules = $this->phone->createRules;

		$validator = Validator::make($attr, $rules);

		if($validator->fails()){
			$errors = $validator->messages();
			return Response::json(['success' => false, 'errors' => $errors], 400);
		}

		//after validation success, lets save the number (through relationship)
		$attr['user_id'] = $user->id;
		$phone = new $this->phone($attr);
		$user->phones()->save($phone);

		return Response::json(['success' => true, 'data' => $phone], 220);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $userId
	 * @param  int  $phoneId
	 * @return Response
	 */
	public function show($phoneId)
	{
		$user = $this->user->where('api_token', '=', $this->token)->first();
		$phone = $this->phone->findOrFail($phoneId);
		return Response::json(['success' => true, 'data' => $phone], 200);
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
	 * @param  int  $phoneId
	 * @return Response
	 */
	public function update($phoneId)
	{
		$attr = Input::get('data');
		$user = $this->user->where('api_token', '=', $this->token)->first();
		$rules = $this->phone->updateRules;
		$validator = Validator::make($attr, $rules);

		if($validator->fails()){
			$errors = $validator->messages();
			return Response::json(['success' => false, 'errors' => $errors], 400);
		}

		//After the validation success, lets update the record.
		$attr['user_id'] = $user->id;
		$phone = $this->phone->findOrFail($phoneId);
		$phone->fill($attr);
		$phone->save();

		return Response::json(['success' => true, 'data' => $phone], 200);	
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($userId, $phoneId)
	{
		$phone = $this->phone->findOrFail($phoneId);
		$phone->delete($phoneId);
		return Response::json(['success' => true],200);
	}

}
