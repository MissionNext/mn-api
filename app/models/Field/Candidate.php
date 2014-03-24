<?php

namespace MissionNext\Models\Field;

use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\ModelInterface;
use MissionNext\Models\DataModel\AppDataModel;
use Illuminate\Database\Eloquent\Builder;

class Candidate extends BaseField implements ModelInterface
{

    protected $table = 'candidate_fields';



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

    /**
     * @param $query
     * @return Builder
     */
    public function scopeFieldsExp($query)
    {

        return $this
            ->select('candidate_fields.id',
                'field_types.name as type',
                'candidate_fields.symbol_key',
                'candidate_fields.name',
                \DB::raw('GROUP_CONCAT(candidate_dictionary.value) as choices'))
            ->leftJoin('field_types', 'field_types.id', '=', 'candidate_fields.type')
            ->leftJoin('candidate_dictionary', 'candidate_dictionary.field_id', '=', 'candidate_fields.id')
            ->groupBy('symbol_key')
            ->orderBy('id');
    }

    /**
     * @param $query
     * @param AppDataModel $dm
     * @return Builder
     */
    public function scopeModelFieldsExp($query, AppDataModel $dm)
    {

        return $this

            ->select('candidate_fields.id',
                'field_types.name as type',
                'candidate_fields.symbol_key',
                'candidate_fields.name',
                \DB::raw('GROUP_CONCAT(candidate_dictionary.value) as choices'))
            ->leftJoin('data_model_candidate_fields', 'candidate_fields.id', '=', 'data_model_candidate_fields.field_id')
            ->leftJoin('field_types', 'field_types.id', '=', 'candidate_fields.type')
            ->leftJoin('candidate_dictionary', 'candidate_dictionary.field_id', '=', 'candidate_fields.id')
            ->where('data_model_candidate_fields.data_model_id', '=', $dm->id)
            ->groupBy('symbol_key')
            ->orderBy('id');
    }

} 