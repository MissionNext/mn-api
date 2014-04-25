<?php


namespace MissionNext\Models\CacheData;


use MissionNext\Models\ModelInterface;
use MissionNext\Models\ModelObservable;
use MissionNext\Models\Profile;
use MissionNext\Models\ProfileInterface;

class UserCachedData extends ModelObservable implements ModelInterface
{
    protected $fillable = array('user_id', 'data');

    /**
     * @param $type
     *
     * @return $this
     */
    public function setType($type)
    {
       $this->table = $type."_cached_profile";

       return $this;
    }

    /**
     * @param ProfileInterface $user
     *
     *  @return $this
     */
    public function setUser(ProfileInterface $user)
    {
       $this->id = $user->id;

       return $this;
    }

    /**
     * @param Profile $profile
     *
     * @return $this
     */
    public function setProfileData(Profile $profile)
    {
        $this->data = $profile->toJson();

        return $this;
    }



} 