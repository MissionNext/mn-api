<?php
namespace MissionNext\Models\Application;


use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model as Eloquent;
use MissionNext\Facade\SecurityContext;
use MissionNext\Models\DataModel\AppDataModel;
use MissionNext\Models\ModelInterface;


class Application extends Eloquent implements ModelInterface
{
    protected $table = 'application';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('public_key', 'private_key');

    /** @var array protected from mass assignment */
    protected $guarded = array('public_key', 'private_key');

    protected $fillable = array('name');

    /**
     * @return HasMany
     */
    public function dataModels()
    {

        return $this->hasMany(static::prefix_ns . '\DataModel\AppDataModel', 'app_id');
    }

    /**
     * @return \Illuminate\Database\Query\Builder
     */
    public function modelFieldsExp()
    {

        return $this->DM()->fieldsExp();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function modelFields()
    {

        return $this->DM()->fields();
    }

    /**
     * @param null $type
     * @return AppDataModel
     */
    public function DM($type = null)
    {
        $type = $type ?: SecurityContext::role();

        return
            $this->dataModels()->whereType($type)->firstOrFail();
    }

} 