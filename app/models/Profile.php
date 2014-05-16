<?php
namespace MissionNext\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use MissionNext\Models\Job\Job;
use MissionNext\Models\User\User;

class Profile extends Eloquent
{
    private $model;

    public function setModel(ProfileInterface $model)
    {
        $this->model = $model;
        $this->app_ids = $model->appIds();
    }

    /**
     * @return User|Job
     */
    public function getModel()
    {

        return $this->model;
    }
} 