<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormGroups extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('form_groups', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string("symbol_key", 60);
            $table->string("name", 60);
            $table->smallInteger('order')->nullable();
            $table->text('meta')->nullable();
            $table->unsignedInteger('form_id');
            $table->foreign("form_id")->references('id')->on('app_forms');
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
		Schema::drop('form_groups');
	}

}
