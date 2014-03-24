<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function(Blueprint $table)
		{
            DB::update('ALTER TABLE `user_roles` MODIFY `u_id` int UNSIGNED');
            DB::update('ALTER TABLE `user_roles` MODIFY `r_id` int UNSIGNED');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function(Blueprint $table)
		{
            DB::update('ALTER TABLE `user_roles` MODIFY `u_id` int SIGNED');
            DB::update('ALTER TABLE `user_roles` MODIFY `r_id` int SIGNED');
		});
	}

}