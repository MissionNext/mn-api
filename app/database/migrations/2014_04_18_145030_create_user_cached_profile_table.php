<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUserCachedProfileTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (DB::getDefaultConnection() === \MissionNext\DB\SqlStatement\Sql::PostgreSQL) {
            DB::statement("CREATE TABLE user_cached_profile
              (user_id serial NOT NULL,
               type VARCHAR (60),
               data json,
               created_at timestamp without time zone NOT NULL,
               updated_at timestamp without time zone NOT NULL,
               CONSTRAINT user_profile_user_id_type_unique UNIQUE  (user_id, type)
            )");

           // DB::statement(" CREATE UNIQUE INDEX profile_data_symbol_key ON user_cached_profile ((data->'profileData'->>'symbol_key')) ");
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (DB::getDefaultConnection() === \MissionNext\DB\SqlStatement\Sql::PostgreSQL) {
            DB::statement("DROP TABLE IF EXISTS user_cached_profile");
        }
    }

}
