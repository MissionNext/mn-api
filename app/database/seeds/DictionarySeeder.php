<?php

class DictionarySeeder extends BaseSeeder
{
    public function run()
    {

        DB::statement($this->getDbStatement()->truncateTable("agency_dictionary"));
        DB::statement($this->getDbStatement()->truncateTable("organization_dictionary"));
        DB::statement($this->getDbStatement()->truncateTable("candidate_dictionary"));





        DB::table('candidate_dictionary')->insert(array(
            array(
                "field_id" => 2,
                "value" => "Argentina",
            ),
            array(
                "field_id" => 2,
                "value" => "Canada",
            ),
            array(
                "field_id" => 2,
                "value" => "Monaco",
            ),
            array(
                "field_id" => 2,
                "value" => "Mexico",
            ),
            array(
                "field_id" => 10,
                "value" => "By email",
            ),
            array(
                "field_id" => 10,
                "value" => "By phone",
            ),
        ));

        DB::table('organization_dictionary')->insert(array(
            array(
                "field_id" => 2,
                "value" => "Spain",
            ),
            array(
                "field_id" => 2,
                "value" => "France",
            ),
            array(
                "field_id" => 2,
                "value" => "Ecuador",
            ),
            array(
                "field_id" => 2,
                "value" => "Germany",
            )
        ));

        DB::table('agency_dictionary')->insert(array(
            array(
                "field_id" => 2,
                "value" => "India",
            ),
            array(
                "field_id" => 2,
                "value" => "Japan",
            ),
            array(
                "field_id" => 2,
                "value" => "Italy",
            ),

        ));


    }
}