<?php
namespace MissionNext\Models\Form;


use MissionNext\Models\ModelInterface;

class FormGroup extends BaseForm implements ModelInterface
{

    protected $table = 'form_groups';

    protected $fillable = array('symbol_key', 'name', 'order', 'meta');

    public function form()
    {

        return $this->belongsTo(static::prefix_ns.'\Form\AppForm');
    }

    public function fields(){

        return $this->hasMany(static::prefix_ns.'\Field\FieldGroup', 'group_id');
    }

} 