<?php
namespace MissionNext\Models\Field;

use Illuminate\Database\Eloquent\Model as Eloquent;
use MissionNext\Models\ModelInterface;

abstract class BaseField extends Eloquent implements IField, ModelInterface
{


    public $timestamps = false;

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

} 