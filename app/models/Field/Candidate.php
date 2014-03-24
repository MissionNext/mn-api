<?php

namespace MissionNext\Models\Field;

use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\ModelInterface;
use MissionNext\Models\DataModel\AppDataModel;
use Illuminate\Database\Eloquent\Builder;

class Candidate extends BaseField implements ModelInterface
{

    protected $table = 'candidate_fields';

    protected $role = BaseDataModel::CANDIDATE;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {

        return $this->belongsToMany('User', 'candidate_profile', 'field_id', 'user_id')->withPivot('value');
    } //@TODO first fields_id because Candiate is Field entity

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function choices()
    {

        return $this->hasMany(static::prefix_ns . '\Dictionary\Candidate', 'field_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function dataModels()
    {

        return $this->belongsToMany(static::prefix_ns . '\DataModel\AppDataModel', 'data_model_candidate_fields', 'field_id', 'data_model_id');
    }

} 