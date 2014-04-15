<?php

namespace MissionNext\Repos\Matching;

use MissionNext\Api\Auth\ISecurityContextAware;
use MissionNext\Api\Auth\SecurityContext;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Field\Organization as OrgFieldModel;
use MissionNext\Models\Job\JobField;
use MissionNext\Models\Matching\Config;
use MissionNext\Repos\AbstractRepository;

class AbstractConfigRepository extends AbstractRepository implements ConfigRepositoryInterface, ISecurityContextAware
{
    protected $modelClassName = Config::class;
    /** @var  SecurityContext */
    public   $sec_context;


    /**
     * @return Config
     */
    public function getModel()
    {
        !empty($this->model->getTable()) ? : $this->setAssocTable();

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

    /**
     * @return $this
     */
    private function setAssocTable()
    {
        $this->model->setTable('matching_'.$this->sec_context->role().'_config');

        return $this;
    }


} 