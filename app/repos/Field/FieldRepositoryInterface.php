<?php

namespace MissionNext\Repos\Field;


use MissionNext\Models\DataModel\AppDataModel;
use MissionNext\Models\Field\BaseField;
use MissionNext\Repos\RepositoryInterface;

interface FieldRepositoryInterface extends  RepositoryInterface {

    /**
     * @return BaseField
     */
    public function getModel();

    public function fieldsExpanded();

    public function modelFieldsExpanded(AppDataModel $dm);

} 