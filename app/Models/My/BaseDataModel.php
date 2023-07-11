<?php
/**
 * Created by WyTcorp.
 * User: WyTcorp
 * Date: 26.09.22
 * Site: lockit.com.ua
 * Email: wild.savedo@gmail.com
 */

namespace App\Models\My;
use Illuminate\Database\Eloquent\Model;

class BaseDataModel extends Model
{
    public const CANDIDATE = "candidate";
    public const ORGANIZATION = "organization";
    public const AGENCY = "agency";

    public const JOB = "job";

    /**
     * @return array
     */
    public static function userRoles(): array
    {
        return [static::CANDIDATE, static::AGENCY, static::ORGANIZATION];
    }

    /**
     * @return array
     */
    public static function allRoles(): array
    {
        return array_merge(static::userRoles(), [static::JOB]);
    }

    /**
     * @param $role
     * @return string
     */
    public static function label($role): string
    {
        $labels = [
            static::CANDIDATE => "Candidate",
            static::ORGANIZATION => "Receiving Organization",
            static::AGENCY => "Service Organization"
        ];

        return $labels[$role];
    }

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
