<?php
namespace MissionNext\Models\Dictionary;

use MissionNext\Models\ModelInterface;

class Candidate extends BaseDictionary implements ModelInterface {

    protected $table = 'candidate_dictionary';

    public function field()
    {
        return $this->belongsTo(static::prefix_ns.'\Field\Candidate', 'field_id');
    }
}