<?php
use MissionNext\Models\DataModel\AppDataModel;

class RoleTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement("SET foreign_key_checks = 0");
        DB::table("roles")->truncate();
        DB::statement("SET foreign_key_checks = 1");
        DB::table('roles')->insert(array(
            array('name' => 'ROLE_CANDIDATE', 'role' => AppDataModel::CANDIDATE),
            array('name' => 'ROLE_ORGANIZATION', 'role' => AppDataModel::ORGANIZATION),
            array('name' => 'ROLE_AGENCY', 'role' => AppDataModel::AGENCY),
        ));
    }
}