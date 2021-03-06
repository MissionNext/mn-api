<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use MissionNext\Models\DataModel\BaseDataModel;

class CreateDictionaryTransTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        $this->createTransTable(BaseDataModel::CANDIDATE);
        $this->createTransTable(BaseDataModel::AGENCY);
        $this->createTransTable(BaseDataModel::ORGANIZATION);
        $this->createTransTable(BaseDataModel::JOB);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop(BaseDataModel::CANDIDATE.'_dictionary_trans');
        Schema::drop(BaseDataModel::AGENCY.'_dictionary_trans');
        Schema::drop(BaseDataModel::ORGANIZATION.'_dictionary_trans');
        Schema::drop(BaseDataModel::JOB.'_dictionary_trans');
	}

    /**
     * @param $role
     */
    private function createTransTable($role)
    {

        Schema::create($role.'_dictionary_trans', function(Blueprint $table) use ($role)
        {
            $table->unsignedInteger('dictionary_id');
            $table->foreign('dictionary_id')->references('id')->on($role.'_dictionary')->onDelete('cascade');

            $table->unsignedInteger('lang_id');
            $table->foreign('lang_id')->references('id')->on('languages')->onDelete('cascade');

            $table->string('value');
        });
    }

}
