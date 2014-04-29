<?php


namespace MissionNext\Models\FolderNotes;


use Illuminate\Database\Eloquent\Model;
use MissionNext\Models\ModelInterface;

class FolderNotes extends Model implements ModelInterface
{
   protected $table = "folders_with_notes";

   protected $fillable = ["user_type", "user_id", "for_user_id", "notes", "folder"];

    /**
     * @param $notes
     *
     * @return $this
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * @param $folder
     *
     * @return $this
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;

        return $this;
    }
} 