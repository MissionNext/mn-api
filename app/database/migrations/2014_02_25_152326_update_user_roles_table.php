<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateUserRolesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('user_roles', function(Blueprint $table)
		{
			$table->renameColumn("u_id", "user_id");
			$table->renameColumn("r_id", "role_id");
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('user_roles', function(Blueprint $table)
		{
            $table->renameColumn("user_id", "u_id");
            $table->renameColumn("role_id", "r_id");
		});
	}

}