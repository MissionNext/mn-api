<?php
namespace MissionNext\Models\Field;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Query\Builder;
use MissionNext\DB\SqlStatement\Sql;
use MissionNext\Models\DataModel\AppDataModel;
use MissionNext\Models\ModelInterface;

abstract class BaseField extends Eloquent implements IField, ModelInterface
{

    public $timestamps = false;

    protected $roleType = null;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fields';


    protected $fillable = array('name', 'multiple', 'symbol_key');



    public function type()
    {

        return $this->belongsTo(static::prefix_ns.'\Field\FieldType', 'type');
    }

    /**
     * @param $query
     * @return Builder
     */
    public function scopeFieldsExp($query)
    {
        $role = $this->roleType;

//        return dd(\DB::select(
//            "select c_f.symbol_key, c_f.id, f_t.name as type,
//                group_concat(c_d.value) as choices
//                      from candidate_fields as c_f
//                   left join field_types as f_t on f_t.id = c_f.type
//                   left join candidate_dictionary as c_d on c_d.field_id = c_f.id
//                   group by  c_f.id, f_t.name
//
//
//        "));


        return $this
            ->select($role.'_fields.id',
                'field_types.name as type',
                $role.'_fields.symbol_key',
                $role.'_fields.name',
                \DB::raw(Sql::getDbStatement()->groupConcat("{$role}_dictionary.value", "choices")))
            ->leftJoin('field_types', 'field_types.id', '=', $role.'_fields.type')
            ->leftJoin($role.'_dictionary', $role.'_dictionary.field_id', '=', $role.'_fields.id')
            ->groupBy($role.'_fields.id', 'field_types.name');
           // ->orderBy($role.'_fields.id');
    }

    /**
     * @param $query
     * @param AppDataModel $dm
     * @return Builder
     */
    public function scopeModelFieldsExp($query, AppDataModel $dm)
    {
        $role = $this->roleType;

        return $this

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
            //->orderBy($role.'_fields.id');
    }

} 