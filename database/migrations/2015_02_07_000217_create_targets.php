<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTargets extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('targets', function(Blueprint $table){
			$table->increments('id');
			$table->char('NPA', 3);
			$table->char('NXX', 3);
			$table->integer('CountryPop');
			$table->integer('ZipCodeCount');
			$table->integer('ZipCodeFreq');
			$table->decimal('Latitude', 12, 8);
			$table->decimal('Longitude', 12, 8);
			$table->char('State', 2);
			$table->string('City');
			$table->string('County');
			$table->string('TimeZone');
			$table->string('ObservesDST');
			$table->string('NXXUseType');
			$table->string('NXXIntroVersion');
			$table->string('ZipCode');
			$table->string('NPANew');
			$table->string('FIPS');
			$table->string('LATA');
			$table->string('Overlay');
			$table->string('RateCenter');
			$table->string('SwitchCLLI');
			$table->string('MSA_CBSA');
			$table->string('MSA_CBSA_Code');
			$table->string('OCN');
			$table->string('Company');
			$table->string('CoverageAreaName');
			$table->char('NPANXX', 6);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('targets');
	}

}
