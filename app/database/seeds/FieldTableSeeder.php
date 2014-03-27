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
                array(
                    "symbol_key" => "skype_handle",
                    "name" => "Skype Handle",
                    "type" => 3,
                ),
                array(
                    "symbol_key" => "day_phone",
                    "name" => "Day Phone",
                    "type" => 3,
                ),
                array(
                    "symbol_key" => "eve_phone",
                    "name" => "Eve Phone",
                    "type" => 3,
                ),
                array(
                    "symbol_key" => "mobile_phone",
                    "name" => "Mobile phone",
                    "type" => 3,
                ),
                array(
                    "symbol_key" => "best_way_to_contact",
                    "name" => "Best way to contact",
                    "type" => 2,
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