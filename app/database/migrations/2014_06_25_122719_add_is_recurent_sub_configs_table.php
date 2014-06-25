<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsRecurentSubConfigsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('subscription_configs', function(Blueprint $table)
		{
			$table->boolean('is_recurrent')->default(false);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('subscription_configs', function(Blueprint $table)
		{
			$table->dropColumn('is_recurrent');
		});
	}

}
