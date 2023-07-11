<?php

namespace App\Models\Matching;

use App\Modules\Api\Facade\SecurityContext;
//use App\Modules\Api\Auth\SecurityContext;
use App\Models\DataModel\BaseDataModel;
use App\Models\Job\JobField;
use App\Models\ModelInterface;
use App\Models\ModelObservable;
use App\Models\Field\Organization as OrgFieldModel;
use Illuminate\Support\Facades\Cache;

class BaseConfig extends ModelObservable implements ModelInterface
{

    protected $table = ''; //must be empty string!!

    protected $role;

    /**
     * @var array
     */
    protected  $matchingFieldModelNames =
        [
            BaseDataModel::JOB => JobField::class ,
            BaseDataModel::ORGANIZATION => OrgFieldModel::class
        ];

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->role = SecurityContext::getInstance()->role();
        $this->table  = $this->role ? 'matching_'.$this->role.'_config' : '' ;
    }

    /**
     * @return string
     */
    protected function matchingFieldModelName()
    {

        return $this->matchingFieldModelNames[$this->role];
    }

}
