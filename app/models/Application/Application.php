<?php
namespace MissionNext\Models\Application;


use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model as Eloquent;
use MissionNext\Facade\SecurityContext;
use MissionNext\Models\DataModel\AppDataModel;
use MissionNext\Models\ModelInterface;
use MissionNext\Models\User\User;


class Application extends Eloquent implements ModelInterface
{
    protected $table = 'application';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('private_key');

    /** @var array protected from mass assignment */
    protected $guarded = array('public_key', 'private_key');

    protected $fillable = array('name');

    /**
     * @return HasMany
     */
    public function dataModels()
    {

        return $this->hasMany(AppDataModel::class, 'app_id');
    }

    public function id()
    {

        return $this->id;
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {

        return $this->belongsToMany(User::class, 'user_apps', 'user_id', 'app_id');
    }


} 