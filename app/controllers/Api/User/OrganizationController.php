<?php


namespace MissionNext\Controllers\Api\User;

use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\User\User;
use MissionNext\Repos\User\UserRepository;
use MissionNext\Repos\User\UserRepositoryInterface;

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
} 