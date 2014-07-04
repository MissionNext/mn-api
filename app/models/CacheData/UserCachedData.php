<?php


namespace MissionNext\Models\CacheData;


use MissionNext\Facade\SecurityContext;

class UserCachedData extends AbstractCachedData
{
    protected $fillable = array('user_id', 'data');

    public static $tableName = null;

    public static $tablePrefix = null;

    /**
     * @param $tableName
     *
     * @return static
     */
    public static function table($tableName)
    {
        static::$tablePrefix =  $tableName;
        static::$tableName = $tableName."_cached_profile";

        return new static;
    }

    public static function getTableName()
    {

        return static::$tableName;
    }


    public function __construct(array $attr = [] )
    {
        $this->table = static::$tablePrefix ? static::getTableName() : $this->roleTableName();

        parent::__construct($attr);
    }

    /**
     * @return string
     */
    protected function roleTableName()
    {

       return SecurityContext::getInstance()->role()."_cached_profile";
    }
} 