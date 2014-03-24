<?php
namespace MissionNext\Models\Form;


use MissionNext\Models\ModelInterface;

class AppForm extends BaseForm implements ModelInterface
{

    protected $table = 'app_forms';

    protected $fillable = array('symbol_key', 'name');

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dataModel()
    {

        return $this->belongsTo(static::prefix_ns.'\DataModel\AppDataModel');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groups()
    {

        return $this->hasMany(static::prefix_ns.'\Form\FormGroup', 'form_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function fields()
    {

        return $this->hasManyThrough(static::prefix_ns.'\Field\FieldGroup', static::prefix_ns.'\Form\FormGroup', 'form_id', 'group_id');
    }

} 