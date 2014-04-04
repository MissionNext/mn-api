<?php
namespace MissionNext\Repos\Field;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use MissionNext\DB\SqlStatement\Sql;
use MissionNext\Models\Field\BaseField;
use MissionNext\Models\User\User as UserModel;
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

    protected function currentFieldModel()
    {
        $this->modelClassName = Field::currentFieldModel($this->securityContext);

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
     *
     * @return FieldDataTransformer
     */
    public function modelFieldsExpanded()
    {
        $role = $this->securityContext->role();

        $dm = $this->securityContext->getApp()->DM();

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

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function modelFields()
    {
        $role = $this->securityContext->role(); // or this->model->roleType
        $dm = $this->securityContext->getApp()->DM();

        return $dm->belongsToMany($this->modelClassName, 'data_model_'.$role.'_fields', 'data_model_id', 'field_id')->withPivot('constraints');
    }

    /**
     * @param UserModel $user
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function profileFields(UserModel $user)
    {
        $role = $this->securityContext->role(); // or this->model->roleType

        return $user->belongsToMany($this->modelClassName, $role.'_profile', 'user_id', 'field_id')->withPivot('value');
    }

} 