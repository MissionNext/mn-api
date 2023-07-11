<?php
namespace App\Models\DataModel;

use App\Models\Job\JobField;
use App\Models\ModelInterface;
use App\Models\Application\Application;
use App\Models\Form\AppForm;
use App\Models\Field\Candidate as CandidateField;
use App\Models\Field\Organization as OrganizationField;
use App\Models\Field\Agency as AgencyField;

class AppDataModel extends BaseDataModel implements ModelInterface
{

    protected $table = 'app_data_model';

    public $timestamps = false;


    public $fillable = ['type', 'app_id'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function app()
    {

        return $this->belongsTo(Application::class, 'app_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function forms($formName = null)
    {
        if($formName !== null){
            return $this->hasMany(AppForm::class, 'data_model_id')->where('data_model_id' , $formName);
        }
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function jobFields()
    {

        return $this->belongsToMany(JobField::class, 'data_model_job_fields', 'data_model_id', 'field_id')->withPivot('constraints');
    }

}
