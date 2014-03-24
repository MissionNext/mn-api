<?php

namespace MissionNext\Models\Dictionary;

use MissionNext\Models\ModelInterface;


class Organization extends BaseDictionary implements ModelInterface {

    protected $table = 'organization_dictionary';

    public function field()
    {
        return $this->belongsTo(static::prefix_ns.'\Field\Organization', 'field_id');
    }
}