<?php


class FieldTableSeeder extends Seeder
{
        public function run()
        {
            DB::statement("SET foreign_key_checks = 0");
            DB::table("candidate_fields")->truncate();
            DB::table("organization_fields")->truncate();
            DB::table("agency_fields")->truncate();
            DB::statement("SET foreign_key_checks = 1");

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