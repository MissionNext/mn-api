<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use MissionNext\Models\DataModel\BaseDataModel;

class CreateUserProfileTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       $create =  function ($role)
        {
            Schema::create($role . '_profile', function (Blueprint $table) use ($role) {
                $table->unsignedInteger('user_id');
                $table->foreign("user_id")->references('id')->on('users')->onDelete('cascade');

                $table->unsignedInteger('field_id');
                $table->foreign("field_id")->references('id')->on($role . '_fields')->onDelete('cascade');

                $table->string('value')->nullable();
            });

        };

       $create(BaseDataModel::CANDIDATE);
       $create(BaseDataModel::ORGANIZATION);
       $create(BaseDataModel::AGENCY);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $drop = function($role){
            Schema::drop($role . '_profile');
        };

       $drop(BaseDataModel::CANDIDATE);
       $drop(BaseDataModel::ORGANIZATION);
       $drop(BaseDataModel::AGENCY);
    }

}
