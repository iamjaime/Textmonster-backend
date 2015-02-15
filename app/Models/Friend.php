<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'friends';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['user_id', 'friend_id'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [];	

	
	public $deleteFriendRules = [
		'friend_id' => 'required'
	];


	/**
	 * Get All friends for a specific user
	 *
	 * @param  int  $userId  The user id that we want friends for.
	 * @return Response
	 */
	public function getFriendInfo($userId)
	{
		$friends = $this->where('user_id', '=', $userId)
		->join('users', 'friends.id', '=', 'users.id')
		->select(
			'users.id as id',
			'users.name as name',
			'users.email as email'
			)
		->get();

		return $friends;
	}
}
