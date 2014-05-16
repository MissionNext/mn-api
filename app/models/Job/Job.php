<?php
namespace MissionNext\Models\Job;

use MissionNext\Models\ModelInterface;
use MissionNext\Models\ModelObservable;
use MissionNext\Models\ProfileInterface;
use MissionNext\Models\User\User as UserModel;

class Job extends ModelObservable implements ProfileInterface
{

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'jobs';

    protected $fillable = array('name', 'symbol_key');

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function jobFields()
    {

        return $this->belongsToMany(JobField::class, 'job_profile', 'job_id', 'field_id')->withPivot('value');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organization()
    {

      return $this->belongsTo(UserModel::class, 'organization_id', 'id');
    }

    /**
     * @param $symbolKey
     *
     * @return $this
     */
    public function setSymbolKey($symbolKey)
    {
        $this->symbol_key = $symbolKey;

        return $this;
    }

    /**
     * @param UserModel $organization
     *
     * @return $this
     */
    public function setOrganization(UserModel $organization)
    {
        $this->organization()->associate($organization);

        return $this;
    }

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function appIds()
    {

        return [$this->app_id];
    }

} 