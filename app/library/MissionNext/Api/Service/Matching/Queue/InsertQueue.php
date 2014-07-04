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
        $mainData = $data["mainData"];
        $matchingData = $data["matchingData"];
        $forUserType = $data["forUserType"];
        $userType = $data["userType"];
        $userId = $data["userId"];
        $matchingClass = $data["matchingClass"];
        $config = $data["config"];
        /** @var  $Matching ServiceMatching */
        $Matching = new $matchingClass($mainData, $matchingData, $config);

        $matchingData = $Matching->matchResults();



        $dateTime = (new \DateTime())->format("Y-m-d H:i:s");

        if (!empty($matchingData)) {
            $insertData = array_map(function ($d) use ($dateTime, $userId, $userType, $forUserType) {
                return
                    [
                        "user_type" => $userType,
                        "user_id" => $d['id'],
                        "for_user_id" => $userId,
                        "for_user_type" => $forUserType,
                        "matching_percentage" => $d['matching_percentage'],
                        "data" => json_encode($d),
                        "created_at" => $dateTime,
                        "updated_at" => $dateTime,
                    ];

            }, $matchingData);


           Results::insert($insertData);

        }
        $job->delete();

    }

} 