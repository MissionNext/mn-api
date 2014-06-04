<?php


namespace MissionNext\Repos\Matching;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use MissionNext\Facade\SecurityContext;
use MissionNext\Models\DataModel\BaseDataModel;
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
                 ->select(DB::raw("distinct on (matching_results.user_type, matching_results.user_id, matching_results.for_user_id, matching_results.for_user_type) matching_results.data, folder_apps.folder, notes.notes") )
                 ->leftJoin("folder_apps", function($join) use ($forUserId, $forUserType, $userType){
                    $join->on("folder_apps.user_id", "=", "matching_results.user_id")
                         ->where("matching_results.for_user_type", "=", $forUserType)
                         ->where("folder_apps.for_user_id", "=", $forUserId)
                         ->where("folder_apps.user_type", "=", $userType)
                         ->where("folder_apps.app_id", "=", SecurityContext::getInstance()->getApp()->id);


                 })
                 ->leftJoin("notes", function($join) use ($forUserId, $forUserType, $userType){
                    $join->on("notes.user_id", "=", "matching_results.user_id")
                        ->where("notes.for_user_id", "=", $forUserId)
                        ->where("notes.user_type", "=", $userType);
                 })
                 ->where("matching_results.for_user_type","=", $forUserType)
                 ->where("matching_results.user_type", "=", $userType)
                 ->where("matching_results.for_user_id", "=", $forUserId)
                 ->whereRaw("ARRAY[?] <@ json_array_text(matching_results.data->'app_ids')", [SecurityContext::getInstance()->getApp()->id])
                 ->get();
         //dd(DB::getQueryLog());
       // dd($matchResults->toArray());

        $matchResults->each(function($el) use (&$data){
            static $i = 0;

            $data[$i] = json_decode($el->data, true);
            $data[$i]['folder'] = $el->folder;
            $data[$i]['notes'] = $el->notes;
            $i++;

        });

        return $data;
    }
} 