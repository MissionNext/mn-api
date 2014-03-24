<?php

namespace MissionNext\Models\Dictionary;

use Illuminate\Database\Eloquent\Model as Eloquent;
use MissionNext\Models\ModelInterface;


abstract class BaseDictionary extends Eloquent implements ModelInterface {


    public $timestamps = false;

    protected $fillable = array('value');

    abstract  public function field();


} 