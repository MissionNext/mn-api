<?php

namespace MissionNext\Models\Dictionary;

use MissionNext\Models\ModelInterface;


class Agency extends BaseDictionary implements ModelInterface {

    protected $table = 'agency_dictionary';

    public function field()
    {
        return $this->belongsTo(static::prefix_ns.'\Field\Agency', 'field_id');
    }
}