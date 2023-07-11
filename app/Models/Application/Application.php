<?php
namespace App\Models\Application;


use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model as Eloquent;
use App\Modules\Api\Facade\SecurityContext;
use App\Models\Configs\AppConfigs;
use App\Models\DataModel\AppDataModel;
use App\Models\Language\LanguageModel;
use App\Models\ModelInterface;
use App\Models\Observers\ApplicationObserver;
use App\Models\Subscription\SubConfig;
use App\Models\User\User;


class Application extends Eloquent implements ModelInterface
{
    protected $table = 'application';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['private_key'];

    /** @var array protected from mass assignment */
    protected $guarded = ['public_key', 'private_key'];

    protected $fillable = [
        'name','private_key','public_key'
    ];

    protected static function boot()
    {
        parent::boot();
        parent::observe(new ApplicationObserver());
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function languages()
    {

        return $this->belongsToMany(LanguageModel::class, 'app_languages', 'app_id', 'lang_id');
    }

    /**
     * @return HasMany
     */
    public function dataModels()
    {

        return $this->hasMany(AppDataModel::class, 'app_id', 'id');
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

        return $this->belongsToMany(User::class, 'user_apps', 'app_id', 'user_id');
    }

    /**
     * @return HasMany
     */
    public function configs()
    {

        return $this->hasMany(AppConfigs::class, 'app_id');
    }

    /**
     * @return HasMany
     */
    public function subConfigs()
    {

        return $this->hasMany(SubConfig::class, 'app_id');
    }


}
