<?php

use MissionNext\Models\Application\Application;
use MissionNext\Models\DataModel\AppDataModel;

class ApplicationSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("SET foreign_key_checks = 0");
        DB::table("application")->truncate();
        DB::table("app_data_model")->truncate();
        DB::statement("SET foreign_key_checks = 1");

        $application1 = new Application();
        $application1->private_key = "654321";
        $application1->public_key = "123456";
        $application1->name = "First App";
        $application1->save();
        $application1->dataModels()->save(AppDataModel::createCandidate());
        $application1->dataModels()->save(AppDataModel::createOrganization());
        $application1->dataModels()->save(AppDataModel::createAgency());

        $application2 = new Application();
        $application2->private_key = "private";
        $application2->public_key = "public";
        $application2->name = "Second App";
        $application2->save();
        $application2->dataModels()->save(AppDataModel::createCandidate());
        $application2->dataModels()->save(AppDataModel::createOrganization());
        $application2->dataModels()->save(AppDataModel::createAgency());


    }

}