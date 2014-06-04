<?php


namespace MissionNext\Controllers\Api\User;

use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\User\User;
use MissionNext\Repos\User\UserRepository;
use MissionNext\Repos\User\UserRepositoryInterface;

class OrganizationController extends BaseController
{

    public function getIndex(User $organization)
    {
         /** @var  $userRepo UserRepository */
        $userRepo = $this->repoContainer[UserRepositoryInterface::KEY];

        return new RestResponse( $userRepo->organizationJobs($organization) );



        dd($userRepo->getModel()->jobs()->get()->toArray());
    }
} 