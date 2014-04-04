<?php

namespace MissionNext\Repos\Field;

use MissionNext\Api\Auth\SecurityContext;
use MissionNext\Api\Exceptions\SecurityContextException;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Field\Agency;
use MissionNext\Models\Field\Candidate;
use MissionNext\Models\Field\Organization;

abstract class Field
{

    private static $secContext;

    /**
     * @param SecurityContext $securityContext
     *
     * @return Candidate|Organization|Agency
     *
     * @throws \MissionNext\Api\Exceptions\SecurityContextException
     */
    public static function currentFieldModel(SecurityContext $securityContext)
    {
        static::$secContext = $securityContext;

        switch ($securityContext->role()) {
            case BaseDataModel::CANDIDATE:
                $modelClassName = Candidate::class;
                break;
            case BaseDataModel::AGENCY:
                $modelClassName = Agency::class;
                break;
            case BaseDataModel::ORGANIZATION:
                $modelClassName = Organization::class;
                break;
            default:
                $modelClassName = \stdClass::class;

        }

        return $modelClassName;

    }


} 