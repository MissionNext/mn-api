<?php

namespace App\Models\Field;

use App\Models\DataModel\BaseDataModel;
use App\Models\Language\LanguageModel;
use App\Models\ModelInterface;
use App\Models\DataModel\AppDataModel;
use App\Models\User\User as UserModel;
use App\Models\Dictionary\Candidate as CandidateDictionary;

class Candidate extends BaseField implements ModelInterface, IRoleField
{

    protected $table = 'candidate_fields';

    protected $roleType = BaseDataModel::CANDIDATE;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {

        return $this->belongsToMany(UserModel::class, 'candidate_profile', 'field_id', 'user_id')->withPivot('value');
    } //@TODO first fields_id because Candiate is Field entity

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function languages()
    {

        return $this->belongsToMany(LanguageModel::class, 'candidate_fields_trans', 'field_id', 'lang_id')->withPivot('name', 'note');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function choices()
    {

        return $this->hasMany(CandidateDictionary::class, 'field_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function dataModels()
    {

        return $this->belongsToMany(AppDataModel::class, 'data_model_candidate_fields', 'field_id', 'data_model_id');
    }

}
