<?php
namespace MissionNext\Models\DataModel;

use Illuminate\Database\Query\Builder;
use MissionNext\Models\Field\FieldStrategy;
use MissionNext\Models\ModelInterface;

class AppDataModel extends BaseDataModel implements ModelInterface
{

    protected $table = 'app_data_model';

    public $timestamps = false;


    /**
     * @return AppDataModel
     */
    public function app()
    {

        return $this->belongsTo(static::prefix_ns . '\Application\Application', 'app_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function forms()
    {

        return $this->hasMany(static::prefix_ns . '\Form\AppForm', 'data_model_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function candidateFields()
    {

        return $this->belongsToMany(static::prefix_ns . '\Field\Candidate', 'data_model_candidate_fields', 'data_model_id', 'field_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function organizationFields()
    {

        return $this->belongsToMany(static::prefix_ns . '\Field\Organization', 'data_model_organization_fields', 'data_model_id', 'field_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function agencyFields()
    {

        return $this->belongsToMany(static::prefix_ns . '\Field\Agency', 'data_model_agency_fields', 'data_model_id', 'field_id');
    }

    /**
     * @return Builder
     */
    public function fieldsExp()
    {
        $fieldModel = FieldStrategy::getModelName();

        return $fieldModel::modelFieldsExp($this);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function fields()
    {
        $method = FieldStrategy::getModelMethod();

        return $this->$method();
    }

} 