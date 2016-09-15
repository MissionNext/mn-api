<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AffiliateFeatured extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('affiliates', function(Blueprint $table)
        {
            $table->boolean('featured')->default(false);
        });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('affiliates', function(Blueprint $table)
        {
            $table->dropColumn('featured');
        });
	}

}
