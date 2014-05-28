<?php


namespace MissionNext\Models\CacheData;


use MissionNext\Facade\SecurityContext;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\ModelInterface;
use MissionNext\Models\Profile;
use MissionNext\Models\ProfileInterface;
use Illuminate\Database\Eloquent\Model;

class UserCachedData extends Model implements ModelInterface
{
    protected $fillable = array('user_id', 'data');


    public function __construct( array $attr = [] )
    {
        $this->table = SecurityContext::getInstance()->role()."_cached_profile";

        parent::__construct($attr);
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

    /**
     * @param $id
     *
     * @return $this
     */
    public function setId($id)
    {

        $this->id = $id;

        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {

        return json_decode($this->data, true);
    }



} 