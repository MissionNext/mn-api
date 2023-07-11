<?php

namespace App\Models\Observers;


use App\Models\Application\Application;
use App\Models\DataModel\AppDataModel;

class ApplicationObserver implements ModelObserverInterface
{

    public function created(Application $model)
    {
        $model->dataModels()->save(AppDataModel::createCandidate());
        $model->dataModels()->save(AppDataModel::createOrganization());
        $model->dataModels()->save(AppDataModel::createAgency());
        $model->dataModels()->save(AppDataModel::createJob());
    }
}
