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
        $users = User::whereIn('id', [1, 2, 3])->get();
        $users->get(2)->roles()->attach(Role::ROLE_ORGANIZATION);
        $users->get(1)->roles()->attach(Role::ROLE_CANDIDATE);

    }
}