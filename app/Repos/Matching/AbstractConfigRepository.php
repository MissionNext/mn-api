<?php

namespace App\Repos\Matching;

use App\Modules\Api\Auth\ISecurityContextAware;
use App\Modules\Api\Auth\SecurityContext;
use App\Models\Matching\Config;
use App\Repos\AbstractRepository;

class AbstractConfigRepository extends AbstractRepository implements ConfigRepositoryInterface, ISecurityContextAware
{
    protected $modelClassName = Config::class;
    /** @var  SecurityContext */
    protected   $sec_context;

    /**
     * @return Config
     */
    public function getModel()
    {
        $this->model = !empty($this->model->getTable()) ? $this->model : new $this->modelClassName;

        return $this->model;
    }

    /**
     * @param SecurityContext $securityContext
     *
     * @return $this
     */
    public function setSecurityContext(SecurityContext $securityContext)
    {
        $this->sec_context = $securityContext;

        return $this;
    }

}
