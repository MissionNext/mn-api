<?php

namespace MissionNext\Controllers\Api\FolderNotes;


use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\FolderNotes\FolderNotes;

abstract class Controller extends BaseController
{
    public function postIndex()
    {
        $request = $this->request->request;
        /** @var  $fNotes FolderNotes */
        $fNotes = FolderNotes::where("user_id", "=", $request->get("user_id"))
            ->where("for_user_id", "=", $request->get("for_user_id"))
            ->where("user_type", "=", $request->get("user_type"))
            ->first();

        if ($fNotes) {
            $this->setFolderNotes($fNotes);
        } else {
            $fNotes = FolderNotes::create($this->request->request->all());
        }

        return new RestResponse($fNotes);
    }

    abstract  protected function setFolderNotes(FolderNotes $fN);

} 