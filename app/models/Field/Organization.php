<?php


namespace MissionNext\Models\Field;

use MissionNext\Models\ModelInterface;
use MissionNext\Models\DataModel\AppDataModel;
use Illuminate\Database\Eloquent\Builder;

class Organization extends BaseField implements ModelInterface
{

    protected $table = 'organization_fields';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {

        return $this->belongsToMany('User', 'organization_profile', 'field_id', 'user_id')->withPivot('value');
    } //@TODO first fields_id because Candiate is Field entity

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function choices()
    {

        return $this->hasMany(static::prefix_ns . '\Dictionary\Organization', 'field_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function dataModels()
    {

        return $this->belongsToMany(static::prefix_ns . '\DataModel\AppDataModel', 'data_model_organization_fields', 'field_id', 'data_model_id');
    }

    /**
     * @param $query
     * @return Builder
     */
    public function scopeFieldsExp($query)
    {


        return $this
            ->select('organization_fields.id',
                'field_types.name as type',
                'organization_fields.symbol_key',
                'organization_fields.name',
                \DB::raw('GROUP_CONCAT(organization_dictionary.value) as choices'))
            ->leftJoin('field_types', 'field_types.id', '=', 'organization_fields.type')
            ->leftJoin('organization_dictionary', 'organization_dictionary.field_id', '=', 'organization_fields.id')
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

            ->select('organization_fields.id',
                'field_types.name as type',
                'organization_fields.symbol_key',
                'organization_fields.name',
                \DB::raw('GROUP_CONCAT(organization_dictionary.value) as choices'))
            ->leftJoin('data_model_organization_fields', 'organization_fields.id', '=', 'data_model_organization_fields.field_id')
            ->leftJoin('field_types', 'field_types.id', '=', 'organization_fields.type')
            ->leftJoin('organization_dictionary', 'organization_dictionary.field_id', '=', 'organization_fields.id')
            ->where('data_model_organization_fields.data_model_id', '=', $dm->id)
            ->groupBy('symbol_key')
            ->orderBy('id');
    }

} 