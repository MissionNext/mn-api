<?php

namespace MissionNext\Repos\User;

use MissionNext\Models\Job\Job;


class JobRepository extends AbstractUserRepository implements JobRepositoryInterface
{
    protected $modelClassName = Job::class;

    /**
     * @return Job
     */
    public function getModel()
    {

        return $this->model;
    }

} 