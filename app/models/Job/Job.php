<?php
namespace MissionNext\Models\Job;

use MissionNext\Models\ModelInterface;
use MissionNext\Models\ModelObservable;

class Job extends ModelObservable implements ModelInterface
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

} 