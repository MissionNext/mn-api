<?php


namespace MissionNext\Models\CacheData;

use MissionNext\Facade\SecurityContext;
use MissionNext\Models\Language\LanguageModel;

class UserCachedDataTrans extends AbstractCachedData
{
    public static $tableName = null;

    /**
     * @param $tableName
     *
     * @return static
     */
    public static function table($tableName)
    {
        static::$tableName = $tableName."_cached_profile_trans";

        return new static;
    }

    public static function getTableName()
    {

        return static::$tableName;
    }


    public function __construct(array $attr = [] )
    {
        $this->table = static::getTableName();

        parent::__construct($attr);
    }

    public function setLang(LanguageModel $languageModel)
    {
        $this->lang_id = $languageModel->id;

        return $this;
    }
}