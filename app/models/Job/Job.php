<?php
namespace MissionNext\Models\Job;

use MissionNext\Models\ModelInterface;
use MissionNext\Models\ModelObservable;
use MissionNext\Models\ProfileInterface;

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
     * @param $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

} 