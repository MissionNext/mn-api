<?php
namespace MissionNext\Models\Field;

use Illuminate\Database\Eloquent\Model as Eloquent;
use MissionNext\Models\ModelInterface;
use MissionNext\Models\Field\Candidate as CandidateModel;

class FieldType extends Eloquent implements ModelInterface
{
    const DATE = 1,
          SELECT = 2,
          INPUT = 3,
          SELECT_MULTIPLE = 4,
          TEXT  = 5,
          RADIO = 6,
          BOOLEAN = 7,
          CHECKBOX_MULTIPLE = 8;

    public $timestamps = false;

    protected $table = 'field_types';

    protected $fillable = array('name');

    public function candidateFields()
    {

        return $this->hasMany(CandidateModel::class, 'type');
    }

} 