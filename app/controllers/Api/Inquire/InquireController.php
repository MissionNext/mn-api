<?php


namespace MissionNext\Controllers\Api\Inquire;


use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\Inquire\Inquire;
use MissionNext\Models\Job\Job;
use MissionNext\Models\User\User;
use MissionNext\Repos\Inquire\InquireRepository;
use MissionNext\Repos\Inquire\InquireRepositoryInterface;

class InquireController extends BaseController
{
    /**
     * @param User $candidate
     * @param Job $job
     *
     * @return RestResponse
     */
    public function postIndex(User $candidate, Job $job)
    {
        /** @var  $repo InquireRepository */
        $repo =  $this->repoContainer[InquireRepositoryInterface::KEY];

        return new RestResponse($repo->inquire($candidate, $job));
    }

    /**
     * @param User $candidate
     * @param Job $job
     *
     * @return RestResponse
     */
    public function postCancel(User $candidate, Job $job)
    {
        /** @var  $repo InquireRepository */
        $repo =  $this->repoContainer[InquireRepositoryInterface::KEY];

        return new RestResponse($repo->cancel($candidate, $job));
    }

    /**
     * @param User $candidate
     *
     * @return RestResponse
     */
    public function getJobs(User $candidate)
    {
        /** @var  $repo InquireRepository */
        $repo =  $this->repoContainer[InquireRepositoryInterface::KEY];

        return new RestResponse($repo->jobs($candidate));
    }

    /**
     * @param User $organization
     * @return RestResponse
     */
    public function getCandidates(User $organization)
    {
        /** @var  $repo InquireRepository */
        $repo =  $this->repoContainer[InquireRepositoryInterface::KEY];

        return new RestResponse($repo->candidates($organization));
    }
} 