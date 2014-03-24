<?php

class DictionarySeeder extends Seeder
{
    public function run()
    {
        DB::statement("SET foreign_key_checks = 0");
        DB::table("candidate_dictionary")->truncate();
        DB::table("organization_dictionary")->truncate();
        DB::statement("SET foreign_key_checks = 1");



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
            )
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

//        $choice = new ChoiceValue(array('value' => 'Banana Republic'));
//
//
//        /** @var  $field Field */
//        $field = Field::find(2);
//
//        $field->choices()->save($choice);

//        $choice = ChoiceValue::find(1);
//        $choice->field()->associate(Field::find(1));
//        $choice->save();

    }
}