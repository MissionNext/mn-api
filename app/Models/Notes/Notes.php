<?php


namespace App\Models\Notes;


use Illuminate\Database\Eloquent\Model;
use App\Models\ModelInterface;

class Notes extends Model implements ModelInterface
{
   protected $table = "notes";

   protected $fillable = ["user_type", "user_id", "for_user_id", "notes"];

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
}
