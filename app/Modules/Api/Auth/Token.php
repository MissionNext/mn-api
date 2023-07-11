<?php
namespace App\Modules\Api\Auth;

use App\Modules\Api\Core\Security\AbstractToken;
use App\Models\Language\LanguageModel;
use App\Models\User\User;

class Token extends AbstractToken
{

    public $created;
    public $hash;
    public $publicKey;
    public $uri;
    public $currentUser;
    public $language;

    /**
     * @return User|null
     */
    public function currentUser()
    {
        return $this->currentUser;
    }

    /**
     * @return LanguageModel
     */
    public function language()
    {

        return $this->language ? : new LanguageModel();
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
