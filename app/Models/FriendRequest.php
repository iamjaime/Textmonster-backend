<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FriendRequest extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'friend_requests';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['requesting_user_id', 'requested_user_id'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [];	

	public $acceptFriendRules = [
		'request_id' => 'required'
	];

	public $makeFriendRules = [
		'requested_user_id' => 'required'
	];

	public $declineFriendRules = [
		'request_id' => 'required'
	];

}
