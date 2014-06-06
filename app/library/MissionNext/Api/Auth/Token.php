<?php
namespace MissionNext\Api\Auth;


use MissionNext\Core\Security\AbstractToken;
use MissionNext\Models\User\User;

class Token extends AbstractToken
{

    public $created;
    public $hash;
    public $publicKey;
    public $uri;
    public $currentUser;

    /**
     * @return User
     */
    public function currentUser()
    {

        return $this->currentUser;
    }

    /**
     * @param array $roles
     *
     * @return $this
     */
    public function setRoles(array $roles = null)
    {

        $this->roles = $roles;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRoles()
    {

        return $this->roles;
    }

    /**
     * @return string|boolean
     */
    public function getRole()
    {
        if (!empty($this->roles)) {

            return $this->roles[0];
        }

        return false;
    }

} 