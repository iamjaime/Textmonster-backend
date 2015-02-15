<?php namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword, SoftDeletes;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'email', 'password'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token', 'deleted_at'];

	/**
	 * The soft deleted date
	 */
	protected $dates = ['deleted_at'];

	public $createRules = [
		'name' => 'required', 
		'email' =>'required|unique:users,email|email', 
		'password' => 'required|confirmed|min:6',
		'password_confirmation' => 'required|min:6',
		'phone_number' => 'required|min:10|unique:phones,phone_number'
	];

	public $updateRules = [
		'name' => 'sometimes|required', 
		'email' =>'sometimes|required|unique:users,email|email',
		'password' => 'sometimes|required|confirmed|min:6',
		'password_confirmation' => 'sometimes|required|min:6'
	];

	public $loginRules = [
		'email' =>'required|email',
		'password' => 'required|min:6',
	];

	public function Phones(){
		return $this->hasMany('App\Models\Phone', 'user_id', 'id');
	}

	public function Subscriptions(){
		return $this->hasMany('App\Models\Subscription', 'user_id', 'id');
	}

	public function Orders(){
		return $this->hasMany('App\Models\Order', 'user_id', 'id');
	}

	public function Messages(){
		return $this->hasMany('App\Models\Message', 'user_id', 'id');
	}
}
