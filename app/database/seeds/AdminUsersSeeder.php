<?php

use MissionNext\Models\Admin\AdminUserModel;

class AdminUsersSeeder extends BaseSeeder {

    public function run() {

        DB::statement($this->getDbStatement()->truncateTable('admin_users'));

        $adminUser1 = new AdminUserModel();
        $adminUser1->username = 'admin';
        $adminUser1->email = 'admin@local.com';
        $adminUser1->password = Hash::make('Flvbygfhjkm');  //Админпароль
        $adminUser1->save();

    }
}