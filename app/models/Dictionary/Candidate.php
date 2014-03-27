<?php
namespace MissionNext\Models\Dictionary;

use MissionNext\Models\ModelInterface;
use MissionNext\Models\Field\Candidate as CandidateField;

class Candidate extends BaseDictionary implements ModelInterface {

    protected $table = 'candidate_dictionary';

    public function field()
    {
        return $this->belongsTo(CandidateField::class, 'field_id');
    }
}