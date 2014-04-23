<?php

use MissionNext\Models\Matching\Config;
use MissionNext\Facade\SecurityContext as Fsc;
use MissionNext\Api\Auth\SecurityContext;
use MissionNext\Models\Job\Job;
use MissionNext\Models\User\User;
use MissionNext\Models\DataModel\BaseDataModel;

class MatchConfigSeeder extends BaseSeeder
{
    public function run()
    {
        DB::statement($this->getDbStatement()->truncateTable("matching_job_config"));
        DB::statement($this->getDbStatement()->truncateTable("matching_organization_config"));
        DB::statement($this->getDbStatement()->truncateTable("user_cached_profile"));
        /** @var  $sc SecurityContext */
        $sc = Fsc::getInstance();

        $sc->setToken((new \MissionNext\Api\Auth\Token())->setRoles([BaseDataModel::JOB])
        ->setApp(\MissionNext\Models\Application\Application::find(1)));

        $configRep = (new \MissionNext\Repos\Matching\ConfigRepository())->setSecurityContext($sc);
        /**
         * (job_id, can_id)
         * matching (2, 6), (3, 2), (5, 5), (6, 4), (9, 13), (10, 1)
         */

        $configs = [
            [
              "matching_type" => Config::MATCHING_EQUAL,
              "weight" => 5,
              "matching_field_id" => 2,
              "main_field_id" => 6,
              "app_id" => 1
            ],
            [
                "matching_type" => Config::MATCHING_EQUAL,
                "weight" => 5,
                "matching_field_id" => 3,
                "main_field_id" => 2,
                "app_id" => 1
            ],
            [
                "matching_type" => Config::MATCHING_EQUAL,
                "weight" => 4,
                "matching_field_id" => 5,
                "main_field_id" => 5,
                "app_id" => 1
            ],
            [
                "matching_type" => Config::MATCHING_EQUAL,
                "weight" => 5,
                "matching_field_id" => 6,
                "main_field_id" => 4,
                "app_id" => 1
            ],
            [
                "matching_type" => Config::MATCHING_EQUAL,
                "weight" => 5,
                "matching_field_id" => 9,
                "main_field_id" => 13,
                "app_id" => 1
            ],
            [
                "matching_type" => Config::MATCHING_EQUAL,
                "weight" => 5,
                "matching_field_id" => 10,
                "main_field_id" => 1,
                "app_id" => 1
            ],

        ];
        $configRep->insert($configs);

        /**
         * set profile cache data
         */
        $userRep = (new \MissionNext\Repos\User\UserRepository())->setSecurityContext($sc);
        $userRep->insertUserCachedData(Job::find(1));
        $userRep->insertUserCachedData(Job::find(2));
        $userRep->insertUserCachedData(Job::find(3));
        $userRep->insertUserCachedData(Job::find(4));
        $userRep->insertUserCachedData(Job::find(5));

        $sc->getToken()->setRoles([BaseDataModel::CANDIDATE]);
        $userRep->insertUserCachedData($userRep->find(2));
    }
}























