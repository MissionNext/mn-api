<?php


namespace MissionNext\Models\Field;

use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\ModelInterface;

class Organization extends BaseField implements ModelInterface
{

    protected $table = 'organization_fields';

    protected $role = BaseDataModel::ORGANIZATION;

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


} 