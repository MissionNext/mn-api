<?php

class FieldTypeSeeder extends Seeder
{
    public function run()
    {
        DB::statement("SET foreign_key_checks = 0");
        DB::table("field_types")->truncate();
        DB::statement("SET foreign_key_checks = 1");

        DB::table('field_types')->insert(array(
            array(
                "id" => 1,
                "name" => "date",
                "multiple" => false,

            ),
            array(
                "id" => 2,
                "name" => "select",
                "multiple" => true,

            ),
            array(
                "id" => 3,
                "name" => "input",
                "multiple" => false,

            )
        ));

    }
}