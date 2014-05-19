<?php


namespace MissionNext\Repos\Matching;


use Illuminate\Database\Eloquent\Builder;
use MissionNext\Facade\SecurityContext;
use MissionNext\Models\Matching\Results;
use MissionNext\Repos\AbstractRepository;

class ResultsRepository extends AbstractRepository implements ResultsRepositoryInterface
{
    protected $modelClassName = Results::class;

    /**
     * @return Results
     */
    public function getModel()
    {

        return $this->model;
    }

    /**
     * @param $forUserType
     * @param $userType
     * @param $forUserId
     *
     * @return array
     */
    public function matchingResults($forUserType, $userType, $forUserId)
    {
        $data = [];

        $matchResults =
            $this->getModel()
                 ->where("for_user_type","=", $forUserType)
                 ->where("user_type", "=", $userType)
                 ->where("for_user_id", "=", $forUserId)
                 ->whereRaw("ARRAY[?] <@ json_array_text(data->'app_ids')", [SecurityContext::getInstance()->getApp()->id])
                 ->get();

        $matchResults->each(function($el) use (&$data){
            $data[] = json_decode($el->data, true);
        });

        return $data;
    }
} 