<?php

class FieldTypeSeeder extends BaseSeeder
{
    public function run()
    {
        DB::statement($this->getDbStatement()->truncateTable("field_types"));

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