<?php
use MissionNext\Models\Field\FieldType;

class FieldTableSeeder extends BaseSeeder
{
        public function run()
        {
            DB::statement($this->getDbStatement()->truncateTable("candidate_fields"));
            DB::statement($this->getDbStatement()->truncateTable("organization_fields"));
            DB::statement($this->getDbStatement()->truncateTable("agency_fields"));


            DB::table('candidate_fields')->insert(array(

                array(
                    "id" => 1,
                    "symbol_key" => "birth_date",
                    "name" => "Birth date",
                    "type" => FieldType::DATE,
                ),

                array(
                    "id" => 2,
                    "symbol_key" => "country",
                    "name" => "Country",
                    "type" => FieldType::SELECT,
                ),
                array(
                    "id" => 3,
                    "symbol_key" => "zip_code",
                    "name" => "Zip Code",
                    "type" => FieldType::INPUT,
                ),
                array(
                    "id" => 4,
                    "symbol_key" => "hobby",
                    "name" => "Hobby",
                    "type" => FieldType::INPUT,
                ),
                array(
                    "id" => 5,
                    "symbol_key" => "occupation",
                    "name" => "Occupation",
                    "type" => FieldType::INPUT,
                ),
                array(
                    "id" => 6,
                    "symbol_key" => "skype_handle",
                    "name" => "Skype Handle",
                    "type" => FieldType::INPUT,
                ),
                array(
                    "id" => 7,
                    "symbol_key" => "day_phone",
                    "name" => "Day Phone",
                    "type" => FieldType::INPUT,
                ),
                array(
                    "id" => 8,
                    "symbol_key" => "eve_phone",
                    "name" => "Eve Phone",
                    "type" => FieldType::INPUT,
                ),
                array(
                    "id" => 9,
                    "symbol_key" => "mobile_phone",
                    "name" => "Mobile phone",
                    "type" => FieldType::INPUT,
                ),
                array(
                    "id" => 10,
                    "symbol_key" => "best_way_to_contact",
                    "name" => "Best way to contact",
                    "type" => FieldType::SELECT,
                ),
                array(
                    "id" => 11,
                    "symbol_key" => "about_me",
                    "name" => "About me",
                    "type" => FieldType::TEXT,
                ),
                array(
                    "id" => 12,
                    "symbol_key" => "agree_with_terms",
                    "name" => "Agree with terms of services",
                    "type" => FieldType::BOOLEAN,
                ),
                array(
                    "id" => 13,
                    "symbol_key" => "favourite_movies",
                    "name" => "Favourite movies",
                    "type" => FieldType::CHECKBOX_MULTIPLE,
                ),
                array(
                    "id" => 14,
                    "symbol_key" => "gender",
                    "name" => "gender",
                    "type" => FieldType::RADIO,
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