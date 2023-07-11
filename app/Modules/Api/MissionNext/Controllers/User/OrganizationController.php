<?php


namespace App\Modules\Api\MissionNext\Controllers\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use App\Modules\Api\Response\RestResponse;
use App\Modules\Api\MissionNext\Controllers\BaseController;
use App\Models\User\User;
use App\Repos\User\UserRepository;
use App\Repos\User\UserRepositoryInterface;

/**
 * Class OrganizationController
 * @package App\Modules\Api\MissionNext\Controllers\User
 */
class OrganizationController extends BaseController
{
    /**
     * @param User $organization
     * @param $userId
     *
     * @return RestResponse
     */
    public function getIndex(User $organization, $userId)
    {
         /** @var  $userRepo UserRepository */
        $userRepo = $this->repoContainer[UserRepositoryInterface::KEY];
        /** @var  $user User */
        $user =  $userRepo->find($userId);

        return new RestResponse( $userRepo->organizationJobsForUser($organization, $user) );
    }

    public function getOrganizationNames()
    {
        $request = Request::instance();
        $org_ids = $request->query->get('organizations');
        $organizations = DB::table('organization_cached_profile')
            ->select(DB::raw("id, data->'profileData'->>'organization_name' as organization_name, data->'profileData'->>'first_name' as first_name, data->'profileData'->>'last_name' as last_name"))
            ->whereIn('id', $org_ids)
            ->get();

        $org_names = [];
        foreach ($organizations as $org) {
            if (!empty($org->organization_name)) {
                $org_names[$org->id] = $org->organization_name;
            } else {
                $org_names[$org->id] = $org->first_name." ".$org->last_name;
            }
        }

        return new RestResponse($org_names);
    }
}
