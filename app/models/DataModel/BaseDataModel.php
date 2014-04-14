<?php
namespace MissionNext\Models\DataModel;

use Illuminate\Database\Eloquent\Model as Eloquent;

class BaseDataModel extends Eloquent
{
    const CANDIDATE = "candidate";
    const ORGANIZATION = "organization";
    const AGENCY = "agency";

    const JOB = "job";


    public static function createCandidate()
    {

        return static::create(["type" => self::CANDIDATE]);
    }

    public static function createOrganization()
    {

        return static::create(["type" => self::ORGANIZATION]);
    }

    public static function createAgency()
    {

        return static::create(["type" => self::AGENCY]);
    }

    public static function createJob()
    {

        return static::create(["type" => self::JOB]);
    }

} 