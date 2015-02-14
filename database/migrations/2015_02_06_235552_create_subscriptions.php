<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscriptions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('subscriptions', function(Blueprint $table){
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			
			$table->foreign('user_id')
			->references('id')
			->on('users')
			->onDelete('cascade');

			$table->integer('service_id')->unsigned();
			
			$table->foreign('service_id')
			->references('id')
			->on('services')
			->onDelete('cascade');

			$table->integer('phone_id')->unsigned();
			
			$table->foreign('phone_id')
			->references('id')
			->on('phones')
			->onDelete('cascade');

			$table->string('link'); //The link that is being advertised. (each service type can have many links)
			$table->boolean('active');
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
		Schema::dropIfExists('subscriptions');
	}

}
