<?php
namespace MissionNext\Repos\Field;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use MissionNext\Api\Exceptions\SecurityContextException;
use MissionNext\DB\SqlStatement\Sql;
use MissionNext\Models\DataModel\AppDataModel;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Field\Agency;
use MissionNext\Models\Field\BaseField;
use MissionNext\Models\Field\Candidate;
use MissionNext\Models\Field\Organization;
use MissionNext\Repos\AbstractRepository;
use MissionNext\Api\Auth\SecurityContext as SecContext;
use stdClass;

class FieldRepository extends AbstractRepository implements FieldRepositoryInterface {

    /** @var \MissionNext\Api\Auth\SecurityContext  */
    protected $securityContext;

    protected $modelClassName = stdClass::class;


    public function setSecurityContext(SecContext $securityContext)
    {
        $this->securityContext = $securityContext;

        $this->currentFieldModel();

        return $this;
    }

    protected  function currentFieldModel()
    {

        switch ($this->securityContext->role()) {
            case BaseDataModel::CANDIDATE:
                $this->modelClassName = Candidate::class;
                break;
            case BaseDataModel::AGENCY:
                $this->modelClassName = Agency::class;
                break;
            case BaseDataModel::ORGANIZATION:
                $this->modelClassName = Organization::class;
                break;
            default:
                throw new SecurityContextException($this->securityContext->role()." role doesn't exists");

        }

        $this->model = new $this->modelClassName;


    }

    /**
     * @return BaseField
     */
    public function getModel()
    {

        return $this->model;
    }

    /**
     * @return FieldDataTransformer
     */
    public function fieldsExpanded()
    {
        $role = $this->securityContext->role();
        /**
         * @var $builder Builder
         */
        $builder = $this->model
            ->select($role.'_fields.id',
                'field_types.name as type',
                $role.'_fields.symbol_key',
                $role.'_fields.name',
                DB::raw(Sql::getDbStatement()->groupConcat("{$role}_dictionary.value", "choices")))
            ->leftJoin('field_types', 'field_types.id', '=', $role.'_fields.type')
            ->leftJoin($role.'_dictionary', $role.'_dictionary.field_id', '=', $role.'_fields.id')
            ->groupBy($role.'_fields.id', 'field_types.name');

        return
            new FieldDataTransformer($builder, new FieldChoiceTransformStrategy() );

    }

    /**
     * @param AppDataModel $dm
     *
     * @return FieldDataTransformer
     */
    public function modelFieldsExpanded(AppDataModel $dm)
    {
        $role = $this->securityContext->role();

        $builder =  $this->model

            ->select($role.'_fields.id',
                'field_types.name as type',
                $role.'_fields.symbol_key',
                $role.'_fields.name',
                'data_model_'.$role.'_fields.constraints',
                \DB::raw(Sql::getDbStatement()->groupConcat("{$role}_dictionary.value", "choices")))
            ->leftJoin('data_model_'.$role.'_fields', $role.'_fields.id', '=', 'data_model_'.$role.'_fields.field_id')
            ->leftJoin('field_types', 'field_types.id', '=', $role.'_fields.type')
            ->leftJoin($role.'_dictionary', $role.'_dictionary.field_id', '=', $role.'_fields.id')
            ->where('data_model_'.$role.'_fields.data_model_id', '=', $dm->id)
            ->groupBy($role.'_fields.id', 'field_types.name', 'data_model_'.$role.'_fields.constraints');

        return
            new FieldDataTransformer($builder, new FieldChoiceTransformStrategy() );
    }

} 