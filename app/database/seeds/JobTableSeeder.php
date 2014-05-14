<?php

class JobTableSeeder extends BaseSeeder
{
    public function run()
    {
        $dateTime = (new DateTime)->format("Y-m-d H:i:s");
        DB::statement($this->getDbStatement()->truncateTable("jobs"));

        DB::table('jobs')->insert(array(
            array('name' => 'First Job', 'symbol_key' =>  "first_job", "organization_id" => 3,
                'created_at'=>$dateTime, 'updated_at'=>$dateTime
            ),
            array('name' => 'Second Job', 'symbol_key' =>  "second_job", "organization_id" => 3,
                'created_at'=>$dateTime, 'updated_at'=>$dateTime
            ),
            array('name' => 'Third Job', 'symbol_key' =>  "third_job", "organization_id" => 3,
                'created_at'=>$dateTime, 'updated_at'=>$dateTime
            ),
            array('name' => 'Fourth Job', 'symbol_key' =>  "fourth_job", "organization_id" => 3,
                'created_at'=>$dateTime, 'updated_at'=>$dateTime
            ),
            array('name' => 'Fifth job', 'symbol_key' =>  "fifth_job", "organization_id" => 3,
                'created_at'=>$dateTime, 'updated_at'=>$dateTime
            ),
            array('name' => 'Six job', 'symbol_key' =>  "six_job", "organization_id" => 7,
                'created_at'=>$dateTime, 'updated_at'=>$dateTime
            ),

        ));

    }
} 