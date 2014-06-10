<?php

namespace MissionNext\Models\Translation;


use Illuminate\Database\Eloquent\Model;
use MissionNext\Facade\SecurityContext;

class Field extends Model
{
    public $timestamps = false;

    protected $table = '';

    protected $fillable = array('field_id', 'lang_id', 'name');

    public function __construct( array $attr = [] )
    {
        $this->table = SecurityContext::getInstance()->role()."_fields_trans";

        parent::__construct($attr);
    }

} 