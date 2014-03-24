<?php
namespace MissionNext\Models\DataModel;

use Illuminate\Database\Eloquent\Model as Eloquent;

class BaseDataModel extends Eloquent
{
    const CANDIDATE = "candidate";
    const ORGANIZATION = "organization";
    const AGENCY = "agency";

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

} 