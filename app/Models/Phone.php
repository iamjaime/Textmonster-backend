<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Phone extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'phones';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['phone_number'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [];	
	
	public $createRules = [
		'phone_number' => 'required|min:10|unique:phones,phone_number'
	];

	public $updateRules = [
		'phone_number' => 'required|min:10|unique:phones,phone_number'
	];

	public function User(){
		return $this->belongsTo('App\Models\User', 'user_id', 'id');
	}
}
