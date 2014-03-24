<?php
use MissionNext\Models\Dictionary\Candidate as CanDictionary;
use MissionNext\Models\Dictionary\Organization as OrgDictionary;
use MissionNext\Models\Dictionary\Agency as AgDictionary;
use MissionNext\Models\Role\Role;
use MissionNext\Models\User\User;

class UserProfileSeeder extends Seeder
{
    public function run()
    {
        DB::statement("SET foreign_key_checks = 0");
        DB::table("candidate_profile")->truncate();
        DB::table("organization_profile")->truncate();
        DB::statement("SET foreign_key_checks = 1");
        /** @var  $candidate User */
        $candidate = Role::find(Role::ROLE_CANDIDATE)->users()->first();
        /** @var  $org User */
        $org = Role::find(Role::ROLE_ORGANIZATION)->users()->first();
        /** @var  $agency User */
        $agency = Role::find(Role::ROLE_AGENCY)->users()->first();

        $choiceCan = CanDictionary::find(4);
        $choiceOrg = OrgDictionary::find(1);
        $choiceAg = AgDictionary::find(1);

        $candidate->candidateFields()->attach(1, array('value' => "1990-11-11"));
        $candidate->candidateFields()->attach(2, array('value' => $choiceCan->value));

        $org->organizationFields()->attach(1, array('value' => "1985-11-11"));
        $org->organizationFields()->attach(2, array('value' => $choiceOrg->value));

        $agency->agencyFields()->attach(1, array('value' => "1988-11-11"));
        $agency->agencyFields()->attach(2, array('value' => $choiceAg->value));



    }
}