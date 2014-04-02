<?php
namespace MissionNext\Repos\Field;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use MissionNext\Api\Exceptions\SecurityContextException;
use MissionNext\DB\SqlStatement\Sql;
use MissionNext\Facade\SecurityContext;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Field\Agency;
use MissionNext\Models\Field\BaseField;
use MissionNext\Models\Field\Candidate;
use MissionNext\Models\Field\Organization;
use MissionNext\Repos\AbstractRepository;

class FieldRepository extends AbstractRepository implements FieldRepositoryInterface {



    /**
     * @return BaseField
     */
    public function getModel()
    {

        return $this->model;
    }

    /**
     *  get model class name based on current role
     */
    public function __construct()
    {
        switch (SecurityContext::role()) {
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
                throw new SecurityContextException(SecurityContext::role()." role doesn't exists");
        }

        parent::__construct();
    }

    /**
     * @return FieldQueryBuilder
     */
    public function fieldsExpanded()
    {
        $role = SecurityContext::role();
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
            new FieldQueryBuilder($builder,

                (new FieldDataFilter())->add(function(Collection $collection){

                    return $collection->each(function ($field) {

                        $field->choices = $field->choices ? explode(",", $field->choices) : null;

                        return $field;
                    });
                } )
            );

    }

} 