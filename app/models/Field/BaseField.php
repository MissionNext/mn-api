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


    protected $fillable = array('name', 'multiple', 'symbol_key', 'default_value');



    public function type()
    {

        return $this->belongsTo(FieldType::class, 'type');
    }

} 