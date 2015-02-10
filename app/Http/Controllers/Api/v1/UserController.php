<?php namespace App\Http\Controllers\Api\v1;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use \Response;
use \Input;
use \Validator;
use \Hash;

use App\Models\User;

class UserController extends Controller {

	public $user;
	
	function __construct(Validator $validator, User $user){
		$this->user = $user;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$users = $this->user->with('phones', 'subscriptions')->get();
		return Response::json(['success' => true, 'data' => $users], 200);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$attr = Input::get('data');
		$rules = $this->user->createRules;

		$validator = Validator::make($attr, $rules);
		
		if ($validator->fails())
		{
		    // The given data did not pass validation
		    $errors = $validator->messages();
			return Response::json(['success' => false, 'errors' => $errors], 400);
		}
		
		//after validation has passed...
		//lets hash the password and save the data
		$attr['password'] = Hash::make($attr['password']);
		$this->user->fill($attr);
		$this->user->save();

		return Response::json(['success' => true, 'data' => $this->user], 220);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$user = $this->user->findOrFail($id);
		return Response::json(['data' => $user], 200);
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
		$attr = Input::get('data');

		$user = $this->user->findOrFail($id);
		$rules = $this->user->updateRules;
		
		$validator = Validator::make($attr, $rules);

		if($validator->fails()){
			$errors = $validator->messages();
			return Response::json(['success' => false, 'errors' => $errors], 400);
		}

		//after validation success lets update the record...
		$user->fill($attr);
		$user->save();

		return Response::json(['success' => true, 'data' => $user], 200);
	}

	/**
	 * Restore the specified user from database
	 *
	 * @param int $id The user id
	 * @return Response
	 */
	public function restore($id){
		$user = $this->user->withTrashed()->where('id', $id);
		$user->restore();
		return Response::json(['success' => true, 'data' => $user->get()], 200);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$user = $this->user->findOrFail($id);
		$user->delete($id);
		return Response::json(['success' => true],200);
	}

}
