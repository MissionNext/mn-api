<?php
namespace MissionNext\Models\DataModel;

use Illuminate\Database\Query\Builder;
use MissionNext\Models\Field\FieldFactory;
use MissionNext\Models\ModelInterface;
use MissionNext\Models\Application\Application;
use MissionNext\Models\Form\AppForm;
use MissionNext\Models\Field\Candidate as CandidateField;
use MissionNext\Models\Field\Organization as OrganizationField;
use MissionNext\Models\Field\Agency as AgencyField;

class AppDataModel extends BaseDataModel implements ModelInterface
{

    protected $table = 'app_data_model';

    public $timestamps = false;


    /**
     * @return AppDataModel
     */
    public function app()
    {

        return $this->belongsTo(Application::class, 'app_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function forms()
    {

        return $this->hasMany(AppForm::class, 'data_model_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function candidateFields()
    {

        return $this->belongsToMany(CandidateField::class, 'data_model_candidate_fields', 'data_model_id', 'field_id')->withPivot('constraints');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function organizationFields()
    {

        return $this->belongsToMany(OrganizationField::class, 'data_model_organization_fields', 'data_model_id', 'field_id')->withPivot('constraints');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function agencyFields()
    {

        return $this->belongsToMany(AgencyField::class, 'data_model_agency_fields', 'data_model_id', 'field_id')->withPivot('constraints');
    }

    /**
     * @return Builder
     */
    public function fieldsExp()
    {

        return FieldFactory::roleBasedModel()->modelFieldsExp($this);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function fields()
    {

        return FieldFactory::fieldsOfModel($this);
    }

} 