<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFolderAddUserid extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('folder', function(Blueprint $table)
		{
			$table->unsignedInteger('user_id')->default(null);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('folder', function(Blueprint $table)
		{
			$table->dropColumn('user_id');
		});
	}

}
