<?php

namespace MissionNext\Models\Field;

use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\ModelInterface;
use MissionNext\Models\DataModel\AppDataModel;
use Illuminate\Database\Eloquent\Builder;
use MissionNext\Models\User\User as UserModel;
use MissionNext\Models\Dictionary\Candidate as CandidateDictionary;

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