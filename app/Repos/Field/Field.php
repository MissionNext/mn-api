<?php

namespace App\Repos\Field;

use App\Modules\Api\Auth\SecurityContext;
use App\Modules\Api\Exceptions\SecurityContextException;
use App\Models\DataModel\BaseDataModel;
use App\Models\Field\Agency;
use App\Models\Field\Candidate;
use App\Models\Field\Organization;
use App\Models\Job\JobField;

abstract class Field
{

    private static $secContext;

    /**
     * @param SecurityContext $securityContext
     *
     * @return Candidate|Organization|Agency
     *
     * @throws \App\Modules\Api\Exceptions\SecurityContextException
     */
    public static function currentFieldModelName(SecurityContext $securityContext)
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
            case BaseDataModel::JOB:
                $modelClassName = JobField::class;
                break;
            default:
                throw new SecurityContextException("Undefined role ".$securityContext->role());

        }

        return $modelClassName;

    }


}
