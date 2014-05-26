<?php

namespace MissionNext\Repos\User;

use MissionNext\Models\Application\Application;
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

    /**
     * @param Application $app
     *
     * @return bool
     */
    public function addApp(Application $app)
    {
        if (!$this->getModel()->hasApp($app)){
            $this->getModel()->app_id = $app->id;

            return true;
        }

        return false;
    }

} 