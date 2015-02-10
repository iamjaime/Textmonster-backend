<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('messages', function(Blueprint $table){
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->foreign('user_id')->
			references('id')
			->on('users')
			->onDelete('cascade');

			$table->integer('service_id')->unsigned();
			$table->foreign('service_id')->
			references('id')
			->on('services')
			->onDelete('cascade');

			$table->integer('target_id')->unsigned();
			$table->foreign('target_id')
			->references('id')
			->on('targets')
			->onDelete('cascade');

			$table->integer('phone_id')->unsigned(); //the phone it was sent from
			$table->foreign('phone_id')
			->references('id')
			->on('phones')
			->onDelete('cascade');

			$table->string('message'); //the message sent
			$table->boolean('ended'); //did the campaign end
			

			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('messages');
	}

}
