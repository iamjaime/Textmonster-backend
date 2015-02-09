<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrders extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orders', function(Blueprint $table){
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

			$table->string('transaction_id');
			$table->integer('payment_amount'); //in cents
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
		Schema::dropIfExists('orders');
	}

}
