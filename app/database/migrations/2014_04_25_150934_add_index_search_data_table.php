<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexSearchDataTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

      \Illuminate\Support\Facades\DB::statement("CREATE INDEX search_data_user_id ON search_data (user_id)");


	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('search_data', function(Blueprint $table)
		{
            \Illuminate\Support\Facades\DB::statement("DROP INDEX search_data_user_id");
		});
	}

}