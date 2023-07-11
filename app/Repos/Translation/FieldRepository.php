<?php

namespace App\Repos\Translation;

use App\Modules\Api\Filter\RouteSecurityFilter;
use App\Models\Translation\Field;
use App\Repos\AbstractRepository;

class FieldRepository extends AbstractRepository implements FieldRepositoryInterface
{
    protected $modelClassName = Field::class;

    private $currentType;

    /**
     * @return Field
     */
    public function getModel()
    {

        return $this->model;
    }

    /**
     * @param $type
     *
     * @return $this
     */
    public function fieldsOfType($type)
    {
        $this->repoContainer->securityContext()->getToken()->setRoles([$type]);
        $this->currentType = $type;

        return new self();
    }
}
