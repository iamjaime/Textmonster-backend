<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Laravel\Cashier\Billable;
use Laravel\Cashier\Contracts\Billable as BillableContract;

class Subscription extends Model implements BillableContract{

	use Billable, SoftDeletes;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'subscriptions';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['active'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['deleted_at'];	

	
	protected $dates = ['trial_ends_at', 'subscription_ends_at', 'deleted_at'];

	public function User(){
		return $this->belongsTo('App\Models\User', 'user_id');
	}
}
