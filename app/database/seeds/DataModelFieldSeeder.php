<?php

use MissionNext\Models\Application\Application;
use MissionNext\Models\DataModel\BaseDataModel;

class DataModelFieldSeeder extends BaseSeeder
{
    public function run()
    {
        DB::statement($this->getDbStatement()->truncateTable("data_model_candidate_fields"));
        DB::statement($this->getDbStatement()->truncateTable("data_model_organization_fields"));

        /** @var  $application Application */
        $application = Application::find(1);

        $application->DM(BaseDataModel::CANDIDATE)
                    ->candidateFields()
                    ->sync([ 1, 2, 3, 4, 5 ]);

        $application->DM(BaseDataModel::ORGANIZATION)
            ->organizationFields()
            ->sync([ 1, 2, 3 ]);

        $application->DM(BaseDataModel::AGENCY)
        ->agencyFields()
        ->sync([ 1, 2, 3, 4 ]);

    }
}