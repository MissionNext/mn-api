<?php

namespace App\Repos\Field;

use App\Modules\Api\Auth\SecurityContext;
use App\Models\Field\BaseField;
use App\Models\ProfileInterface;
use App\Repos\RepositoryInterface;

interface FieldRepositoryInterface extends  RepositoryInterface {

    /**
     * @return BaseField
     */
    public function getModel();

    public function setSecurityContext(SecurityContext $securityContext);

    public function fieldsExpanded();

    public function modelFieldsExpanded();

    public function modelFields();

    public function profileFields(ProfileInterface $user);

    const KEY = "model_field";

}
