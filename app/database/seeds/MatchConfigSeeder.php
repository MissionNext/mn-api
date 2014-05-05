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
        DB::statement($this->getDbStatement()->truncateTable("organization_cached_profile"));
        DB::statement($this->getDbStatement()->truncateTable("job_cached_profile"));
        DB::statement($this->getDbStatement()->truncateTable("agency_cached_profile"));
        DB::statement($this->getDbStatement()->truncateTable("candidate_cached_profile"));
        /** @var  $sc SecurityContext */
        $sc = Fsc::getInstance();

        $sc->setToken((new \MissionNext\Api\Auth\Token())->setRoles([BaseDataModel::JOB])
        ->setApp(\MissionNext\Models\Application\Application::find(1)));

        $configRep = (new \MissionNext\Repos\Matching\ConfigRepository())->setSecurityContext($sc);
        /**
         * (job_id, can_id)
         * matching (1, 6), (3, 2), (5, 5), (6, 4), (2, 13), (10, 1)
         */

        $configs = [
            [
              "matching_type" => Config::MATCHING_EQUAL,
              "weight" => 4,
              "matching_field_id" => 1, //"job_title" - select
              "main_field_id" => 6, // skype_handle - input
              "app_id" => 1
            ],
            [
                "matching_type" => Config::MATCHING_EQUAL,
                "weight" => 5,
                "matching_field_id" => 3, // job_location - input - job
                "main_field_id" => 2, // country - select - candidate
                "app_id" => 1
            ],
            [
                "matching_type" => Config::MATCHING_LIKE,
                "weight" => 4,
                "matching_field_id" => 5,//position type
                "main_field_id" => 5,//occupation
                "app_id" => 1
            ],
            [
                "matching_type" => Config::MATCHING_EQUAL,
                "weight" => 5,
                "matching_field_id" => 6, //subject_speciality
                "main_field_id" => 4, // hobby
                "app_id" => 1
            ],
            [
                "matching_type" => Config::MATCHING_EQUAL,
                "weight" => 5,
                "matching_field_id" => 2, //favourite_movies - checkbox - job
                "main_field_id" => 13,//favourite_movies - checkbox - candidate
                "app_id" => 1
            ],
            [
                "matching_type" => Config::MATCHING_GREATER, //job less than candidate
                "weight" => 4,
                "matching_field_id" => 10, //birth_date - date -job
                "main_field_id" => 1, // birth_date - candidate
                "app_id" => 1
            ],

            [
                "matching_type" => Config::MATCHING_EQUAL, //job less than candidate
                "weight" => 4,
                "matching_field_id" => 9, //favourite movies - multiple -job
                "main_field_id" => 10, // best way to contact - select single -  candidate
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
        $userRep->insertUserCachedData($userRep->find(4));

        $sc->getToken()->setRoles([BaseDataModel::AGENCY]);
        $userRep->insertUserCachedData($userRep->find(1));

        $sc->getToken()->setRoles([BaseDataModel::ORGANIZATION]);
        $userRep->insertUserCachedData($userRep->find(3));
        $userRep->insertUserCachedData($userRep->find(5));

        $configRep = (new \MissionNext\Repos\Matching\ConfigRepository())->setSecurityContext($sc);
        $orgConfig = [
            [
                "matching_type" => Config::MATCHING_GREATER_OR_EQUAL, //canidadate date greater or equal
                "weight" => 3,
                "matching_field_id" => 1, //"birth_date" - date
                "main_field_id" => 1, // birth_date - date
                "app_id" => 1
            ],
            [
                "matching_type" => Config::MATCHING_EQUAL,
                "weight" => 4,
                "matching_field_id" => 2, //"contry" -
                "main_field_id" => 2, // country -
                "app_id" => 1
            ],
            [
                "matching_type" => Config::MATCHING_LESS,
                "weight" => 4,
                "matching_field_id" => 3, //"zipcode" -
                "main_field_id" => 3, // zipcode -
                "app_id" => 1
            ],
        ];

        $configRep->insert($orgConfig);

    }
}























