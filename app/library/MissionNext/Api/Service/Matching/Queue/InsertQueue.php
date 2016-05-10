<?php


namespace MissionNext\Api\Service\Matching\Queue;

use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use MissionNext\Api\Service\Matching\Matching as ServiceMatching;
use MissionNext\Models\Matching\Results;


class InsertQueue
{

    public function fire($job, $data)
    {
        $start = microtime(true);

        $mainData = $data["mainData"];
        $matchingData = $data["matchingData"];
        $forUserType = $data["forUserType"];
        $userType = $data["userType"];
        $userId = $data["userId"];
        $matchingClass = $data["matchingClass"];
        $config = $data["config"];
        $app_id = $data["app_id"];

        /** @var  $Matching ServiceMatching */
        $Matching = new $matchingClass($mainData, $matchingData, $config);

        $matchingData = $Matching->matchResults();

        $dateTime = (new \DateTime())->format("Y-m-d H:i:s");

        if (!empty($matchingData)) {

            $insertData = [];
            $deleteIds = [];
            $insertOppositeData = [];
            $i = 0;
            $k = 0;
            foreach($matchingData as $match){

                $insertData[$k]['user_type'] = $userType;
                $insertData[$k]['user_id'] = $match['id'];
                $insertData[$k]['for_user_id'] = $userId;
                $insertData[$k]['for_user_type'] = $forUserType;
                $insertData[$k]['matching_percentage'] = $match['matching_percentage'];
                $insertData[$k]['app_id'] = $app_id;
                $insertData[$k]['data'] = json_encode($match);
                $insertData[$k]['created_at'] = $dateTime;
                $insertData[$k]['updated_at'] = $dateTime;

                $deleteIds[] = $match['id'];

                $otherMatch = Results::where('user_id', $userId)->where('for_user_id', $match['id'])->where('app_id', $app_id)->first();

                if(!is_null($otherMatch)) {

                    $results = json_decode($otherMatch->data, true);
                    $results['results'] = $match['results'];
                    $results['matching_percentage'] = $match['matching_percentage'];

                    $insertOppositeData[$i]['user_type'] = $otherMatch['user_type'];
                    $insertOppositeData[$i]['user_id'] = $otherMatch['user_id'];
                    $insertOppositeData[$i]['for_user_id'] = $otherMatch['for_user_id'];
                    $insertOppositeData[$i]['for_user_type'] = $otherMatch['for_user_type'];
                    $insertOppositeData[$i]['matching_percentage'] = $match['matching_percentage'];
                    $insertOppositeData[$i]['app_id'] = $app_id;
                    $insertOppositeData[$i]['data'] = json_encode($results);
                    $insertOppositeData[$i]['created_at'] = $dateTime;
                    $insertOppositeData[$i]['updated_at'] = $dateTime;

                    $i++;
                }
                $k++;
            }

            Results::where('app_id', $app_id)->where('for_user_id', $userId)->whereIn('user_id', $deleteIds)->delete();
            Results::insert($insertData);

            if($i) {
                Results::where('app_id', $app_id)->where('user_id', $userId)->whereIn('for_user_id', $deleteIds)->delete();
                Results::insert($insertOppositeData);
            }
        }

        $job->delete();

        $stop = microtime(true);
        $res = $stop - $start;

        echo " # " . $res . ' #### ';
    }

}

