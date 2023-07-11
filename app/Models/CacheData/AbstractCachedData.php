<?php

namespace App\Models\CacheData;

use App\Models\AbstractDynamicModel;
use App\Models\DataModel\BaseDataModel;
use App\Models\ModelInterface;
use App\Models\Profile;
use App\Models\ProfileInterface;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractCachedData extends AbstractDynamicModel
{
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
     * @return array
     */
    public function getData()
    {

        return json_decode($this->data, true);
    }
}
