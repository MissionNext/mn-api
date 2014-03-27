<?php

namespace MissionNext\Models\Dictionary;

use MissionNext\Models\ModelInterface;
use MissionNext\Models\Field\Agency as AgencyField;

class Agency extends BaseDictionary implements ModelInterface {

    protected $table = 'agency_dictionary';

    public function field()
    {
        return $this->belongsTo(AgencyField::class, 'field_id');
    }
}