<?php


class FieldTableSeeder extends BaseSeeder
{
        public function run()
        {
            DB::statement($this->getDbStatement()->truncateTable("candidate_fields"));
            DB::statement($this->getDbStatement()->truncateTable("organization_fields"));
            DB::statement($this->getDbStatement()->truncateTable("agency_fields"));


            DB::table('candidate_fields')->insert(array(

                array(
                    "symbol_key" => "birth_date",
                    "name" => "Birth date",
                    "type" => 1,
                ),

                array(
                    "symbol_key" => "country",
                    "name" => "Country",
                    "type" => 2,
                ),
                array(
                    "symbol_key" => "zip_code",
                    "name" => "Zip Code",
                    "type" => 3,
                ),
                array(
                    "symbol_key" => "hobby",
                    "name" => "Hobby",
                    "type" => 3,
                ),
                array(
                    "symbol_key" => "occupation",
                    "name" => "Occupation",
                    "type" => 3,
                ),
            ));

            DB::table('organization_fields')->insert(array(

                array(
                    "symbol_key" => "birth_date",
                    "name" => "Birth date",
                    "type" => 1,
                ),

                array(
                    "symbol_key" => "country",
                    "name" => "Country",
                    "type" => 2,
                ),
                array(
                    "symbol_key" => "zip_code",
                    "name" => "Zip Code",
                    "type" => 3,
                ),
                array(
                    "symbol_key" => "hobby",
                    "name" => "Hobby",
                    "type" => 3,
                ),
                array(
                    "symbol_key" => "occupation",
                    "name" => "Occupation",
                    "type" => 3,
                ),
                array(
                    "symbol_key" => "first_name",
                    "name" => "First name",
                    "type" => 3,
                ),
                array(
                    "symbol_key" => "last_name",
                    "name" => "Last name",
                    "type" => 3,
                ),

            ));

            DB::table('agency_fields')->insert(array(

                array(
                    "symbol_key" => "birth_date",
                    "name" => "Birth date",
                    "type" => 1,
                ),

                array(
                    "symbol_key" => "country",
                    "name" => "Country",
                    "type" => 2,
                ),

                array(
                    "symbol_key" => "first_name",
                    "name" => "First name",
                    "type" => 3,
                ),

                array(
                    "symbol_key" => "last_name",
                    "name" => "Last name",
                    "type" => 3,
                ),


            ));

       }
}