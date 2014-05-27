<?php

use MissionNext\Models\Admin\AdminUserModel;

class AdminUsersSeeder extends BaseSeeder {

    public function run() {

        DB::table('adminusers')->delete();

        $user = Sentry::createUser(array(
            'email'     => 'admin@loc.com',
            'password'  => 'Flvbygfhjkm',  // Админпароль
            'activated' => true,
            'username'  => 'admin',
        ));

    }
}