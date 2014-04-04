<?php

namespace MissionNext\Repos\Form;


use MissionNext\Models\Form\AppForm;
use MissionNext\Repos\AbstractRepository;

class FormRepository extends AbstractRepository implements FormRepositoryInterface
{
    protected $modelClassName = AppForm::class;

    /**
     * @return AppForm
     */
    public function getModel()
    {

        return $this->model;
    }

} 