<?php
use MissionNext\Models\Dictionary\Candidate as CanDictionary;
use MissionNext\Models\Dictionary\Organization as OrgDictionary;
use MissionNext\Models\Dictionary\Agency as AgDictionary;
use MissionNext\Models\Role\Role;
use MissionNext\Models\User\User;
use MissionNext\Models\Job\Job;

class UserProfileSeeder extends BaseSeeder
{
    public function run()
    {
        DB::statement($this->getDbStatement()->truncateTable("candidate_profile"));
        DB::statement($this->getDbStatement()->truncateTable("organization_profile"));

        /** @var  $candidate User */
        $candidate = Role::find(Role::ROLE_CANDIDATE)->users()->first();
        /** @var  $org User */
        $org = Role::find(Role::ROLE_ORGANIZATION)->users()->first();
        /** @var  $agency User */
        $agency = Role::find(Role::ROLE_AGENCY)->users()->first();



        $choiceCan = CanDictionary::find(4);
        $choiceOrg = OrgDictionary::find(1);
        $choiceAg = AgDictionary::find(1);
        /** @var  $job1 Job */
        $job1 = Job::find(1);
        $job1->jobFields()->attach(5, ["value" => "Administrator" ]);
        $job1->jobFields()->attach(6, ["value" => "English" ]);
        $job1->jobFields()->attach(2, ["value" => "Some title" ]);
        /** @var  $job2 Job */
        $job2 = Job::find(2);
        $job2->jobFields()->attach(5, ["value" => "Administrator" ]);
        $job2->jobFields()->attach(6, ["value" => "English" ]);
        $job2->jobFields()->attach(2, ["value" => "Some title" ]);
        /** @var  $job3 Job */
        $job3 = Job::find(3);
        $job3->jobFields()->attach(5, ["value" => "Administratorik" ]);
        $job3->jobFields()->attach(6, ["value" => "English" ]);
        $job3->jobFields()->attach(2, ["value" => "Some title" ]);
        /** @var  $job4 Job */
        $job4 = Job::find(4);
        $job4->jobFields()->attach(5, ["value" => "Administratorik" ]);
        $job4->jobFields()->attach(6, ["value" => "English" ]);
        $job4->jobFields()->attach(2, ["value" => "Some title" ]);
        $job4->jobFields()->attach(9, ["value" => "Bamby" ]);
        $job4->jobFields()->attach(9, ["value" => "Buratino" ]);
        $job4->jobFields()->attach(10, ["value" => "1990-11-11" ]);

        /** @var  $job5 Job */
        $job5 = Job::find(5);
        $job5->jobFields()->attach(5, ["value" => "Administratorik" ]);
        $job5->jobFields()->attach(6, ["value" => "Englishik" ]);
        $job5->jobFields()->attach(2, ["value" => "Some title" ]);
        $job5->jobFields()->attach(3, ["value" => "Mexico" ]);
        $job5->jobFields()->attach(9, ["value" => "Bamby" ]);
        $job5->jobFields()->attach(9, ["value" => "Buratino" ]);

        $candidate->candidateFields()->attach(1, array('value' => "1990-11-11"));
        $candidate->candidateFields()->attach(2, array('value' => $choiceCan->value));
        $candidate->candidateFields()->attach(13, array('value' => "Bamby"));
        $candidate->candidateFields()->attach(13, array('value' => "Buratino"));
        $candidate->candidateFields()->attach(5, array('value' => "Administrator"));
        $candidate->candidateFields()->attach(4, array('value' => "English"));
        $candidate->candidateFields()->attach(6, array('value' => "Some title"));
        /**
         * (job_id, can_id)
         * matching (2, 6), (3, 2), (5, 5), (6, 4), (9, 13), (10, 1)
         */


        $org->organizationFields()->attach(1, array('value' => "1985-11-11"));
        $org->organizationFields()->attach(2, array('value' => $choiceOrg->value));

        $agency->agencyFields()->attach(1, array('value' => "1988-11-11"));
        $agency->agencyFields()->attach(2, array('value' => $choiceAg->value));


    }
}