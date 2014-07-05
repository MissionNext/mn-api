<?php


namespace MissionNext\Models\CacheData;


class UserCachedData extends AbstractCachedData
{
    protected $fillable = array('user_id', 'data');

    protected  static $tableName = null;
    protected  static $tableRolePrefix = null;

    protected  static $tablePrefix = 'cached_profile';
} 