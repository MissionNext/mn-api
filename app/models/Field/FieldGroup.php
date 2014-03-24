<?php
namespace MissionNext\Models\Field;

use Illuminate\Database\Eloquent\Model as Eloquent;
use MissionNext\Models\ModelInterface;

class FieldGroup extends Eloquent implements ModelInterface
{

    protected $table = 'group_fields';

    protected $fillable = array('symbol_key', 'order', 'meta');

    public function formGroup()
    {

        return $this->belongsTo(static::prefix_ns.'\Form\FormGroup', 'group_id');
    }

} 