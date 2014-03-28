<?php
namespace MissionNext\Models\Field;

use Illuminate\Database\Eloquent\Model as Eloquent;
use MissionNext\Models\ModelInterface;
use MissionNext\Models\Field\Candidate as CandidateModel;

class FieldType extends Eloquent implements ModelInterface
{

    public $timestamps = false;

    protected $table = 'field_types';

    protected $fillable = array('name');

    public function candidateFields()
    {

        return $this->hasMany(CandidateModel::class, 'type');
    }

} 