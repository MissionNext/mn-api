<?php
use MissionNext\Models\User\User;
use MissionNext\Models\Role\Role;

class UserRoleTableSeeder extends BaseSeeder
{
    public function run()
    {
        DB::statement($this->getDbStatement()->truncateTable("user_roles"));

        User::find(3)->roles()->attach(Role::ROLE_ORGANIZATION);
        User::find(2)->roles()->attach(Role::ROLE_CANDIDATE);
        User::find(1)->roles()->attach(Role::ROLE_AGENCY);
        User::find(4)->roles()->attach(Role::ROLE_CANDIDATE);
        User::find(5)->roles()->attach(Role::ROLE_ORGANIZATION);

    }
}