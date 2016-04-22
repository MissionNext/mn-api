<?php


namespace MissionNext\Repos\Matching;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use MissionNext\Api\Service\DataTransformers\UserCachedDataStrategy;
use MissionNext\Api\Service\DataTransformers\UserCachedTransformer;
use MissionNext\Api\Service\Matching\TransData;
use MissionNext\Facade\SecurityContext;
use MissionNext\Models\CacheData\UserCachedDataTrans;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Matching\Results;
use MissionNext\Models\Subscription\Partnership;
use MissionNext\Models\Subscription\Subscription;
use MissionNext\Repos\AbstractRepository;
use MissionNext\Api\Auth\SecurityContext as SC;

class ResultsRepository extends AbstractRepository implements ResultsRepositoryInterface
{
    protected $modelClassName = Results::class;

    const PAGINATION = 100;

    /**
     * @return Results
     */
    public function getModel()
    {

        return $this->model;
    }

    /**
     * @return SC
     */
    public function securityContext()
    {

        return SecurityContext::getInstance();
    }

    /**
     * @param $forUserType
     * @param $userType
     * @param $forUserId
     * @param $options
     *
     * @return array
     */
    public function matchingResults($forUserType, $userType, $forUserId, $options = null)
    {
//        $start = microtime(true);

        if(isset($options))
            extract($options);

        $org_select = '';
        if ($userType === BaseDataModel::JOB) {
            $org_select = ", organization_cached_profile.data->'profileData'->>'organization_name' as org_name";
        }

        $builder =
            $this->getModel()
                 ->select(DB::raw("distinct on (matching_results.user_type, matching_results.user_id, matching_results.for_user_id, matching_results.for_user_type, matching_results.matching_percentage) matching_results.data, folder_apps.folder, notes.notes, subscriptions.partnership, subscriptions.id as sub_id $org_select") )
                 ->leftJoin("folder_apps", function($join) use ($forUserId, $forUserType, $userType){
                    $join->on("folder_apps.user_id", "=", "matching_results.user_id")
                         ->where("matching_results.for_user_type", "=", $forUserType)
                         ->where("folder_apps.for_user_id", "=", $this->securityContext()->getToken()->currentUser()->id)
                         ->where("folder_apps.user_type", "=", $userType)
                         ->where("folder_apps.app_id", "=", $this->securityContext()->getApp()->id());


                 })
                 ->leftJoin("notes", function($join) use ($forUserId, $forUserType, $userType){
                     $join->on("notes.user_id", "=", "matching_results.user_id")
                        ->where("notes.for_user_id", "=", $this->securityContext()->getToken()->currentUser()->id)
                        ->where("notes.user_type", "=", $userType);
                 })
                 ->where("matching_results.for_user_type","=", $forUserType)
                 ->where("matching_results.user_type", "=", $userType)
                 ->where("matching_results.for_user_id", "=",  $forUserId)
                 ->where("matching_results.app_id", "=",  SecurityContext::getInstance()->getApp()->id);
//                 ->whereRaw("ARRAY[?] <@ json_array_text(matching_results.data->'app_ids')", [SecurityContext::getInstance()->getApp()->id]);

            if(isset($updates)) {
                $updates .= '-01-01';
                $builder->leftJoin("users", "users.id", "=", 'matching_results.user_id')
                    ->where('users.last_login', '>=', $updates);
            }

            if ($userType === BaseDataModel::JOB ) {
                $builder->leftJoin("organization_cached_profile", "organization_cached_profile.id", "=", DB::raw("(matching_results.data->'organization'->>'id')::int"));
            }

            $builder = $userType === BaseDataModel::JOB ? $builder->leftJoin("subscriptions", "subscriptions.user_id", "=",  DB::raw("(matching_results.data->'organization'->>'id')::int"))
                                                     : $builder->leftJoin("subscriptions", "subscriptions.user_id", "=",  DB::raw("(matching_results.data->>'id')::int"));

            $builder->where('subscriptions.app_id', '=', $this->securityContext()->getApp()->id() )
                ->where('subscriptions.status', '<>', Subscription::STATUS_CLOSED)
                ->where(function($query){
                    $query->where('subscriptions.partnership', "<>", Partnership::LIMITED)
                        ->orWhereNull('subscriptions.partnership');

                })
                ->whereNotNull('subscriptions.id')
                ->where(function($query){
                    $query->where('subscriptions.status', '<>', Subscription::STATUS_EXPIRED)
                          ->orWhere('subscriptions.price', "=", 0);

                });

            if(isset($rate))
                $builder->where('matching_results.matching_percentage', '>=', $rate);

            $builder->orderBy('matching_results.matching_percentage', 'desc');

            $result = (new UserCachedTransformer($builder, new UserCachedDataStrategy()))->paginate(static::PAGINATION);

         return (new TransData($this->securityContext()->getToken()->language(), $userType, $result->toArray()))->get();

    }
} 