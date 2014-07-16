<?php
namespace MissionNext\Models\User;


class ExtendedUser extends User
{

    protected $appends = ['roleName', 'appsData'];

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
} 