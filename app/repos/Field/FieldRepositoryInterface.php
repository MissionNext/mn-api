<?php

namespace MissionNext\Repos\Field;


use MissionNext\Models\Field\BaseField;
use MissionNext\Repos\RepositoryInterface;

interface FieldRepositoryInterface extends  RepositoryInterface {

    /**
     * @return BaseField
     */
    public function getModel();

} 