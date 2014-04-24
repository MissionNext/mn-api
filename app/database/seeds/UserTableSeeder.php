<?php

/**
 * Class UserTableSeeder
 */
class UserTableSeeder extends BaseSeeder
{
    public function run()
    {
        $dateTime = (new DateTime)->format("Y-m-d H:i:s");
        DB::statement($this->getDbStatement()->truncateTable("users"));

        DB::table('users')->insert(array(
            array('email' => 'admin@example.com', 'password' =>  Hash::make('123456'),
                  'username' => 'admin',
                  'created_at'=>$dateTime, 'updated_at'=>$dateTime
            ),
            array('email' => 'user@example.com', 'password' =>  Hash::make('654321'),
                  'username' => 'user',
                  'created_at'=>$dateTime, 'updated_at'=>$dateTime
            ),
            array('email' => 'org@example.com', 'password' =>  Hash::make('654321'),
                'username' => 'organizator',
                'created_at'=>$dateTime, 'updated_at'=>$dateTime
            ),
            array('email' => 'candidate1@example.com', 'password' =>  Hash::make('654321'),
                'username' => 'candidate1',
                'created_at'=>$dateTime, 'updated_at'=>$dateTime
            ),
        ));

    }
}