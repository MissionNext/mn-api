<?php

namespace MissionNext\Controllers\Api\FolderNotes;


use MissionNext\Models\FolderNotes\FolderNotes;


class FolderController extends Controller
{
    /**
     * @param FolderNotes $fNotes
     *
     * @return FolderNotes
     */
    protected function setFolderNotes(FolderNotes $fNotes)
    {
        $fNotes->setFolder($this->request->request->get("folder"));
        $fNotes->save();

        return $fNotes;
    }
} 