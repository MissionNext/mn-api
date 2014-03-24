<?php
namespace MissionNext\Models\Field;

use Illuminate\Database\Eloquent\Model as Eloquent;
use MissionNext\Models\ModelInterface;

class FieldType extends Eloquent implements ModelInterface
{

    public $timestamps = false;

    protected $table = 'field_types';

    protected $fillable = array('name');

    public function candidateFields()
    {

        return $this->hasMany(static::prefix_ns.'\Field\Candidate', 'type');
    }

} 