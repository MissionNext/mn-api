<?php

namespace MissionNext\Controllers\Api\FolderNotes;


use MissionNext\Models\FolderNotes\FolderNotes;

class NoteController extends Controller
{
    /**
     * @param FolderNotes $fNotes
     *
     * @return FolderNotes
     */
    protected function setFolderNotes(FolderNotes $fNotes)
    {
        $fNotes->setNotes($this->request->request->get("notes"));
        $fNotes->save();

        return $fNotes;
    }
} 