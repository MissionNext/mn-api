<?php

namespace App\Models\Dictionary;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\ModelInterface;


abstract class BaseDictionary extends Eloquent implements ModelInterface {


    public $timestamps = false;

    protected $fillable = array('value', 'field_id', 'order', 'meta');

    abstract  public function field();

    /**
     * @return BelongsToMany
     */
    abstract public function languages();

}
