<?php
namespace App\Models\Form;

use App\Models\ModelInterface;
use App\Models\DataModel\AppDataModel;
use App\Models\Field\FieldGroup;

class AppForm extends BaseForm implements ModelInterface
{

    protected $table = 'app_forms';

    protected $fillable = array('symbol_key', 'name');

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function dataModel()
    {

        return $this->belongsTo(AppDataModel::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function groups()
    {

        return $this->hasMany(FormGroup::class, 'form_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function fields()
    {

        return $this->hasManyThrough(FieldGroup::class, FormGroup::class, 'form_id', 'group_id');
    }

}
