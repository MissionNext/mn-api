<?php

namespace MissionNext\Models\Observers;


use MissionNext\Models\FolderNotes\FolderNotes;

class FolderObserver implements ModelObserverInterface
{

    public function deleted($model)
    {

        FolderNotes::where("folder","=",$model->title)
                   ->where("user_type","=", $model->role)
                   ->update(["folder" => null]);
    }

    public function updated($model)
    {

        FolderNotes::where("folder","=",$model->getOriginal()['title'])
                    ->where("user_type","=", $model->role)
                    ->update(["folder" => $model->title]);
    }
} 