<?php

namespace MissionNext\Models\Matching;

use MissionNext\Facade\SecurityContext;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Job\JobField;
use MissionNext\Models\ModelInterface;
use MissionNext\Models\ModelObservable;
use MissionNext\Models\Application\Application as AppModel;
use MissionNext\Models\Field\Candidate as CandidateFieldModel;
use MissionNext\Models\Field\Organization as OrgFieldModel;

class Config extends ModelObservable implements ModelInterface
{
    protected $table = '';

    public $timestamps = false;

    const MATCHING_EQUAL = 1,
          MATCHING_GREATER_OR_EQUAL = 2,
          MATCHING_GREATER = 3,
          MATCHING_LESS_OR_EQUAL = 4,
          MATCHING_LESS = 5,
          MATCHING_LIKE = 6;

    /**
     * @var array
     */
    private $matchingFieldModelNames =
        [
            BaseDataModel::JOB => JobField::class ,
            BaseDataModel::ORGANIZATION => OrgFieldModel::class
        ];

    protected $fillable = array('weight', 'matching_type');

    public function __construct(array $attributes = array()){
         parent::__construct($attributes);
         $this->table = 'matching_'.SecurityContext::getInstance()->role().'_config';
    }

    /**
     * @param $matchingType
     *
     * @return $this
     */
    public function setMatchingType($matchingType)
    {
        $this->matching_type = $matchingType;

        return $this;
    }

    /**
     * @param $weight
     *
     * @return $this
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function matchingField()
    {

        return $this->belongsTo($this->matchingFieldModelName(), SecurityContext::getInstance()->role().'_field_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function candidateField()
    {

        return $this->belongsTo(CandidateFieldModel::class, 'candidate_field_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function application()
    {

        return $this->belongsTo(AppModel::class, 'app_id', 'id');
    }

    /**
     * @return string
     */
    protected function matchingFieldModelName()
    {

        return $this->matchingFieldModelNames[SecurityContext::getInstance()->role()];
    }

} 