<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFriendRequests extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('friend_requests', function(Blueprint $table){
			$table->increments('id');
			
			$table->integer('requesting_user_id')->unsigned();
			$table->foreign('requesting_user_id')->
			references('id')
			->on('users')
			->onDelete('cascade');

			$table->integer('requested_user_id')->unsigned();
			$table->foreign('requested_user_id')->
			references('id')
			->on('users')
			->onDelete('cascade');

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('friend_requests');
	}

}
