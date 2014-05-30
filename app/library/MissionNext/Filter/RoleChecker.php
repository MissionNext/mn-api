<?php

namespace MissionNext\Filter;

use Illuminate\Routing\Route as Router;
use Illuminate\Http\Request as LRequest;
use Illuminate\Support\Facades\Route;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Job\Job;
use MissionNext\Models\User\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RoleChecker
{
    const CHECK = "check";

    public function check(Router $route, LRequest $request)
    {
        $userModel = new User();
        $jobModel = new Job();

        $roleInputs =  [ "candidate" => ["model" =>$userModel, "role" => BaseDataModel::CANDIDATE] ,
                         "job" => ["model" => $jobModel, "role" => BaseDataModel::JOB],
                         "agency" =>["model" => $userModel, "role" => BaseDataModel::AGENCY],
                         "organization"  => ["model" => $userModel, "role" => BaseDataModel::ORGANIZATION]
                        ];


        /** @var $model User */
        foreach($roleInputs as $input => $el){
            if ($userId = Route::input($input)){
                $model = $el["model"]->find($userId);
                if (!$model || !$model->hasRole($el["role"])){

                    throw new NotFoundHttpException();
                }
                $route->setParameter($input, $model);
            }
        }
    }
} 