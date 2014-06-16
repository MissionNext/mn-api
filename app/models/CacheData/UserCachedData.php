<?php


namespace MissionNext\Models\CacheData;


use MissionNext\Facade\SecurityContext;


class UserCachedData extends AbstractCachedData
{
    protected $fillable = array('user_id', 'data');


    public function __construct( array $attr = [] )
    {
        $this->table = SecurityContext::getInstance()->role()."_cached_profile";

        parent::__construct($attr);
    }
} 