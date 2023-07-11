<?php

namespace App\Models\Dictionary;

use App\Models\Language\LanguageModel;
use App\Models\ModelInterface;
use App\Models\Field\Organization as OrganizationField;

class Organization extends BaseDictionary implements ModelInterface {

    protected $table = 'organization_dictionary';

    public function field()
    {
        return $this->belongsTo(OrganizationField::class, 'field_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function languages()
    {

        return $this->belongsToMany(LanguageModel::class, 'organization_dictionary_trans', 'dictionary_id', 'lang_id')->withPivot('value');
    }
}
