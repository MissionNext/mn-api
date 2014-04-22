<?php


class UserCachedProfileSeeder extends BaseSeeder
{
    public function run()
    {
       DB::statement($this->getDbStatement()->truncateTable('user_cached_profile'));
       return;

       $data = json_encode((object)['username' => 'user', 'email' => 'some@mail.ru', 'profileData' => ['date'=>'1990-11-11', 'favourite_movies' => ['candy','bamby']]]);
       DB::statement("INSERT INTO user_cached_profile VALUES (2, 'candidate', '{$data}',  '2001-11-11 20:00:00', '2001-12-12 09:00:00') ");

        $data = json_encode((object)['username' => 'user', 'email' => 'some@mail.ru', 'profileData' => new stdClass()]);
        DB::statement("INSERT INTO user_cached_profile VALUES (4, 'candidate', '{$data}',  '2001-11-11 20:00:00', '2001-12-12 09:00:00') ");

       $data =
           json_encode((object)['name' => 'Some job', 'symbol_key' => 'some_job', 'organization_id' => 3 , 'profileData' => ['job_title'=>'priest', 'second_title' => 'bomoko', 'alternate_speciality' => ['pm','author'] ] ] );
       DB::statement("INSERT INTO user_cached_profile VALUES (2, 'job', '{$data}', '2002-11-11 00:00:00', '2002-10-10 19:00:00') ");

       $data =
           json_encode((object)['name' => 'New job', 'symbol_key' => 'new_job', 'organization_id' => 3 , 'profileData' => ['job_title'=>'priest', 'second_title' => 'smeko', 'alternate_speciality' => ['pm','driver'] ] ]  );
       DB::statement("INSERT INTO user_cached_profile VALUES (3, 'job', '{$data}', '2003-11-11 00:00:00', '2003-10-10 19:00:00') ");



    }
} 