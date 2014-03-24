<?php

/**
 * Class UserTableSeeder
 */
class UserTableSeeder extends Seeder
{
    public function run()
    {
        $dateTime = (new DateTime)->format("Y:m:d H:i:s");
        DB::statement("SET foreign_key_checks = 0");
        DB::table("users")->truncate();
        DB::statement("SET foreign_key_checks = 1");
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
        ));

    }
}