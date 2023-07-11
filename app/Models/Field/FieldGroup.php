<?php
namespace App\Models\Field;

use Illuminate\Database\Eloquent\Model as Eloquent;
use App\Models\ModelInterface;
use App\Models\Form\FormGroup;

class FieldGroup extends Eloquent implements ModelInterface
{

    protected $table = 'group_fields';

    protected $fillable = array('symbol_key', 'order', 'meta');

    public function formGroup()
    {

        return $this->belongsTo(FormGroup::class, 'group_id');
    }

}
