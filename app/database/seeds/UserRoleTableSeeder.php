<?php
use MissionNext\Models\User\User;
use MissionNext\Models\Role\Role;

class UserRoleTableSeeder extends Seeder
{
    public function run()
    {
        DB::statement("SET foreign_key_checks = 0");
        DB::table("user_roles")->truncate();
        DB::statement("SET foreign_key_checks = 1");
        User::find(3)->roles()->attach(Role::ROLE_ORGANIZATION);
        User::find(2)->roles()->attach(Role::ROLE_CANDIDATE);
        User::find(1)->roles()->attach(Role::ROLE_AGENCY);

    }
}