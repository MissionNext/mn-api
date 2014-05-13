<?php

namespace MissionNext\Models\Observers;


use MissionNext\Models\FolderNotes\FolderNotes;

class FolderObserver implements ModelObserverInterface
{

    public function deleted($model)
    {

        FolderNotes::where("folder","=",$model->title)->update(["folder" => null]);
    }

    public function updated($model)
    {

        FolderNotes::where("folder","=",$model->getOriginal()['title'])->update(["folder" => $model->title]);
    }
} 