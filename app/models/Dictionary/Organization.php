<?php

namespace MissionNext\Models\Dictionary;

use MissionNext\Models\ModelInterface;
use MissionNext\Models\Field\Organization as OrganizationField;

class Organization extends BaseDictionary implements ModelInterface {

    protected $table = 'organization_dictionary';

    public function field()
    {
        return $this->belongsTo(OrganizationField::class, 'field_id');
    }
}