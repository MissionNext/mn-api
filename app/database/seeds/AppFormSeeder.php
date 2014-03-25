<?php
use MissionNext\Models\Application\Application;
use MissionNext\Models\Form\AppForm;
use MissionNext\Models\Form\FormGroup;
use MissionNext\Models\Field\FieldGroup;

class AppFormSeeder extends BaseSeeder
{
    public function run()
    {
        DB::statement($this->getDbStatement()->truncateTable("app_forms"));
        DB::statement($this->getDbStatement()->truncateTable("form_groups"));
        DB::statement($this->getDbStatement()->truncateTable("group_fields"));
        /** @var  $application Application */
        return;
        $application = Application::find(1);
        $canDM = $application->candidateDMs()->first();

        $appForm1 = new AppForm();
        $appForm1->dataModel()->associate($canDM);
        $appForm1->symbol_key = "registration";
        $appForm1->name = "Registration";
        $appForm1->save();

        $appForm2 = new AppForm();
        $appForm2->dataModel()->associate($canDM);
        $appForm2->symbol_key = "some_info";
        $appForm2->name = "Some information";
        $appForm2->save();


        $formGroup1 = new FormGroup();
        $formGroup1->symbol_key = "group_1";
        $formGroup1->name = "First Group";
        $formGroup1->order = 1;
        $formGroup1->form()->associate($appForm1);
        $formGroup1->save();

        $fieldGroup1 = new FieldGroup();
        $fieldGroup1->symbol_key = "birth_date";
        $fieldGroup1->order = 1;
        $fieldGroup1->formGroup()->associate($formGroup1);
        $fieldGroup1->save();

        $fieldGroup2 = new FieldGroup();
        $fieldGroup2->symbol_key = "country";
        $fieldGroup2->order = 2;
        $fieldGroup2->formGroup()->associate($formGroup1);
        $fieldGroup2->save();

        $formGroup2 = new FormGroup();
        $formGroup2->symbol_key = "some_group";
        $formGroup2->name = "Some Group";
        $formGroup2->order = 1;
        $formGroup2->form()->associate($appForm2);
        $formGroup2->save();

        $fieldGroup4 = new FieldGroup();
        $fieldGroup4->symbol_key = "hobby";
        $fieldGroup4->order = 1;
        $fieldGroup4->formGroup()->associate($formGroup2);
        $fieldGroup4->save();

    }
}