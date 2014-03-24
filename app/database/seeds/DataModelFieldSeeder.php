<?php

use MissionNext\Models\Application\Application;
use MissionNext\Models\DataModel\BaseDataModel;

class DataModelFieldSeeder extends Seeder
{
    public function run()
    {
        DB::statement("SET foreign_key_checks = 0");
        DB::table("data_model_candidate_fields")->truncate();
        DB::table("data_model_organization_fields")->truncate();
        DB::statement("SET foreign_key_checks = 1");
        /** @var  $application Application */
        $application = Application::find(1);

        $application->DM(BaseDataModel::CANDIDATE)
                    ->candidateFields()
                    ->sync([ 1, 2, 3, 4, 5 ]);

        $application->DM(BaseDataModel::CANDIDATE)
            ->organizationFields()
            ->sync([ 1, 2, 3 ]);

    }
}