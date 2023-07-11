<?php
namespace App\Models\Dictionary;

use App\Models\Language\LanguageModel;
use App\Models\ModelInterface;
use App\Models\Field\Candidate as CandidateField;

class Candidate extends BaseDictionary implements ModelInterface {

    protected $table = 'candidate_dictionary';

    public function field()
    {
        return $this->belongsTo(CandidateField::class, 'field_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function languages()
    {

        return $this->belongsToMany(LanguageModel::class, 'candidate_dictionary_trans', 'dictionary_id', 'lang_id')->withPivot('value');
    }
}
