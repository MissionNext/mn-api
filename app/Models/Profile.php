<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use App\Models\Job\Job;
use App\Models\User\User;

class Profile extends Eloquent
{
    private $model;

    public function setModel(ProfileInterface $model)
    {
        $apps = $model->appData();

        $this->model = $model;
        $this->app_ids = $apps->pluck("id");
        $this->app_names = $apps->pluck("public_key");
    }

    /**
     * @return User|Job
     */
    public function getModel()
    {

        return $this->model;
    }
}
