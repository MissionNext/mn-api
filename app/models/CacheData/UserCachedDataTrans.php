<?php


namespace MissionNext\Models\CacheData;

use MissionNext\Facade\SecurityContext;
use MissionNext\Models\Language\LanguageModel;

class UserCachedDataTrans extends AbstractCachedData
{
    protected $fillable = array('user_id', 'data', 'lang_id');


    public function __construct( array $attr = [], $type = null )
    {
        $this->table = !$type ? SecurityContext::getInstance()->role()."_cached_profile_trans"
                              : $type."_cached_profile_trans";

        parent::__construct($attr);
    }

    public function setLang(LanguageModel $languageModel)
    {
        $this->lang_id = $languageModel->id;

        return $this;
    }
}