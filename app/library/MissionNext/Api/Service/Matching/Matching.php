<?php

namespace MissionNext\Api\Service\Matching;


use MissionNext\Api\Exceptions\SecurityContextException;
use MissionNext\Facade\SecurityContext;
use MissionNext\Filter\RouteSecurityFilter;
use MissionNext\Models\Field\FieldType;
use MissionNext\Repos\Field\Field;

abstract class Matching
{
    /**
     * @param $matchData
     * @param $matchAgainstData
     * @param $matchConfig
     */
    public function __construct($matchData, $matchAgainstData, $matchConfig)
    {
        $this->matchData = $matchData;
        $this->matchAgainstData = $matchAgainstData;
        $this->matchConfig = $matchConfig;
    }

    private $selectFieldTypes = [FieldType::SELECT, FieldType::SELECT_MULTIPLE, FieldType::CHECKBOX, FieldType::RADIO];

    protected $matchData, $matchAgainstData, $matchConfig;

    /**
     * @param $type
     *
     * @return array
     *
     * @throws \MissionNext\Api\Exceptions\SecurityContextException
     */
    public  function selectFieldsOfType($type)
    {
        if (!RouteSecurityFilter::isAllowedRole($type)) {

            throw new SecurityContextException("'$type' role doesn't exists", SecurityContextException::ON_SET_ROLE);
        }
        SecurityContext::getInstance()->getToken()->setRoles([$type]);
        $fieldModelName = Field::currentFieldModelName(SecurityContext::getInstance());

        return
            array_fetch((new $fieldModelName)->whereIn("type",
                $this->selectFieldTypes)->get()->toArray(), 'symbol_key');
    }

    abstract public function matchResults();

} 