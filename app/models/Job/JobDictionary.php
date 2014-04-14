<?php


namespace MissionNext\Models\Job;


use MissionNext\Models\Dictionary\BaseDictionary;

class JobDictionary extends BaseDictionary
{
    protected $table = 'job_dictionary';

    public function field()
    {
        return $this->belongsTo(JobField::class, 'field_id');
    }
} 