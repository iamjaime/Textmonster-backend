<?php namespace App\Http\Controllers\Api\v1;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use \Response;
use \Input;
use \Validator;
use \Hash;
use \Auth;

use App\Models\User;

class UserController extends Controller {

	public $user;
	public $token;

	function __construct(Validator $validator, User $user, Request $request){
		$this->token = $request->header('X-AUTH-TOKEN');
		$this->user = $user;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$users = $this->user->with('phones', 'subscriptions')->where('api_token', '=', $this->token)->get();
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
		$user = $this->user->where('api_token', '=', $this->token)->first();
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

		$user = $this->user->where('api_token', '=', $this->token)->first();
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
		$user = $this->user->where('api_token', '=', $this->token)->first();
		$id = $user->id;
		$user->delete($id);
		return Response::json(['success' => true],200);
	}

	/**
	 * Authentiacte the user
	 */
	public function authenticate()
	{
		$attrs = Input::get('data');
		$rules = $this->user->loginRules;
		$validator = Validator::make($attrs, $rules);
		if($validator->fails()){
			$errors = $validator->messages();
			return Response::json(['success' => false, 'errors' => $errors],400);
		}
		
		//after the validation is successful, lets check the login credentials.
		if (Auth::attempt(['email' => $attrs['email'], 'password' => $attrs['password']]))
        {
        	//lets make an auth token for them....
        	$user = Auth::user();
        	$token = hash('sha256', $this->random(10),false);
        	$user->api_token = $token;
        	$user->save();

        	return Response::json([
        		'success' => true, 
        		'data' => $user
        		], 200);    
        }

		return Response::json(['success' => false, 'errors' => 'The email address and password combination are invalid.'], 400);	
	}

	/**
	 * Generate a more truly "random" alpha-numeric string.
	 *
	 * @param  int     $length
	 * @return string
	 */
	private function random($length = 16)
	{
	    if (function_exists('openssl_random_pseudo_bytes'))
	    {
	        $bytes = openssl_random_pseudo_bytes($length * 2);

	        if ($bytes === false)
	        {
	            throw new \RuntimeException('Unable to generate random string.');
	        }

	        return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
	    }

	    return $this->quickRandom($length);
	}

	/**
	 * Generate a "random" alpha-numeric string.
	 *
	 * Should not be considered sufficient for cryptography, etc.
	 *
	 * @param  int     $length
	 * @return string
	 */
	private function quickRandom($length = 16)
	{
	    $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	    return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
	}
}
