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

	function __construct(User $user, Phone $phone){
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
		$phone = $this->user->phones();
		return Response::json(['success' => true, 'data' => $phone], 200);
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
	public function store($userId)
	{
		$attr = Input::get('data');
		$user = $this->user->findOrFail($userId);
		$rules = $this->phone->createRules;

		$validator = Validator::make($attr, $rules);

		if($validator->fails()){
			$errors = $validator->messages();
			return Response::json(['success' => false, 'errors' => $errors], 400);
		}

		//after validation success, lets save the number (through relationship)
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
	public function show($userId, $phoneId)
	{
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
	 * @param  int  $userId
	 * @param  int  $phoneId
	 * @return Response
	 */
	public function update($userId, $phoneId)
	{
		$attr = Input::get('data');
		$user = $this->user->findOrFail($userId);
		$rules = $this->phone->updateRules;
		$validator = Validator::make($attr, $rules);

		if($validator->fails()){
			$errors = $validator->messages();
			return Response::json(['success' => false, 'errors' => $errors], 400);
		}

		//After the validation success, lets update the record.
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
