<?php


namespace MissionNext\Models\CacheData;


use MissionNext\Facade\SecurityContext;
use MissionNext\Models\ModelInterface;
use MissionNext\Models\Profile;
use MissionNext\Models\ProfileInterface;
use Illuminate\Database\Eloquent\Model;

class UserCachedData extends Model implements ModelInterface
{
    protected $fillable = array('user_id', 'data');


    public function __construct( array $attr = [])
    {
        $this->table = SecurityContext::getInstance()->role()."_cached_profile";

        parent::__construct($attr);
    }
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