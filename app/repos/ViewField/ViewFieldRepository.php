<?php

namespace MissionNext\Repos\ViewField;


use MissionNext\Models\Field\FieldGroup;
use MissionNext\Repos\AbstractRepository;

class ViewFieldRepository extends AbstractRepository implements ViewFieldRepositoryInterface
{

    protected $modelClassName = FieldGroup::class;

    /**
     * @return FieldGroup
     */
    public function getModel()
    {

        return $this->model;
    }

} 