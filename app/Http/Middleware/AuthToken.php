<?php namespace App\Http\Middleware;

use Closure;

use App\Models\User;
use \Response;
class AuthToken {

	public $user;

	function __construct(User $user){
		$this->user = $user;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{

		$payload = $request->header('X-Auth-Token');
	    $user =  $this->user->where('api_token',$payload)->first();

	    if(!$payload || !$user) {

	        $response = Response::json([
	            'success' => false,
	            'error' => [
	            	'message' => 'The X-AUTH-TOKEN Header is required.',
	            	'code' => 401
	            ]],
	            401
	        );

	        $response->header('Content-Type', 'application/json');
	    	return $response;
	    }
		
		return $next($request);
	}

}
