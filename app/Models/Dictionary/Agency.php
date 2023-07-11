<?php

namespace App\Models\Dictionary;

use App\Models\Language\LanguageModel;
use App\Models\ModelInterface;
use App\Models\Field\Agency as AgencyField;

class Agency extends BaseDictionary implements ModelInterface {

    protected $table = 'agency_dictionary';

    public function field()
    {
        return $this->belongsTo(AgencyField::class, 'field_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function languages()
    {

        return $this->belongsToMany(LanguageModel::class, 'agency_dictionary_trans', 'dictionary_id', 'lang_id')->withPivot('value');
    }
}
