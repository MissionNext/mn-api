<?php

namespace App\Repos\FormGroup;

use Illuminate\Database\Query\Builder;
use App\Modules\Api\Auth\ISecurityContextAware;
use App\Modules\Api\Auth\SecurityContext;
use App\DB\SqlStatement\Sql;
use App\Models\Form\FormGroup;
use App\Repos\AbstractRepository;
use App\Repos\Field\FieldToArrayTransformStrategy;
use App\Repos\Field\FieldDataTransformer;

class FormGroupRepository extends AbstractRepository implements FormGroupRepositoryInterface, ISecurityContextAware
{
    protected $modelClassName = FormGroup::class;
    /** @var  SecurityContext */
    protected $securityContext;

    public function setSecurityContext(SecurityContext $securityContext)
    {

        $this->securityContext = $securityContext;
    }

    /**
     * @return FormGroup
     */
    public function getModel()
    {

        return $this->model;
    }

    /**
     * @return Builder
     */
    public function dependentFields()
    {

       $dm = $this->securityContext->getApp();
       //$dm = $this->securityContext->getApp()->DM();
        //dd($dm->id);
       $builder =   $this->getModel()
            ->select('form_groups.depends_on', 'form_groups.depends_on_option', \DB::raw(Sql::getDbStatement()->groupConcat('group_fields.symbol_key','symbol_keys')) )
            ->leftJoin('app_forms', 'app_forms.id', '=', 'form_groups.form_id')
            ->leftJoin('app_data_model', 'app_data_model.id', '=', 'app_forms.data_model_id')
            ->leftJoin('group_fields', 'group_fields.group_id', '=', 'form_groups.id')
            ->where('app_forms.data_model_id', '=', $dm->id)
            ->where('form_groups.depends_on', '<>', '')
            ->groupBy('form_groups.depends_on', 'form_groups.depends_on_option');

        return new FieldDataTransformer($builder, new FieldToArrayTransformStrategy(['choices','symbol_keys']));
    }

}
