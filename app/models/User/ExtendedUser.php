<?php
namespace MissionNext\Models\User;


class ExtendedUser extends User
{

    protected $appends = ['roleName', 'appsData', 'appsIds'];

    /**
     * @return string
     */
    public function getRoleNameAttribute()
    {

        return $this->role();
    }

    /**
     * @return array
     */
    public function getAppsDataAttribute()
    {

        return $this->apps()->get(['id', 'name'])->toArray();
    }

    public function getAppsIdsAttribute()
    {

        return array_fetch($this->apps_data, 'id');
    }
} 