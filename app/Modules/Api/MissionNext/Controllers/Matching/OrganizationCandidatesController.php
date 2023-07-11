<?php


namespace App\Modules\Api\MissionNext\Controllers\Matching;


use Illuminate\Support\Facades\Queue;
use App\Modules\Api\Response\RestResponse;
use App\Modules\Api\Service\Matching\OrganizationCandidates;
use App\Modules\Api\MissionNext\Controllers\BaseController;
use App\Models\Configs\UserConfigs;
use App\Models\DataModel\BaseDataModel;
use App\Repos\CachedData\UserCachedRepository;
use App\Modules\Api\Service\Matching\Queue\OrganizationCandidates as OrgCandidatesQueue;

/**
 * Class OrganizationCandidatesController
 * @package App\Modules\Api\Controllers\Matching
 */
class OrganizationCandidatesController extends BaseController
{
    /**
     * @param $organizationId
     *
     * @return RestResponse
     */
    public function getIndex($organizationId)
    {

        $old_updates = UserConfigs::where(['app_id' => $this->securityContext()->getApp()->id, 'user_id' => $organizationId, 'key' => 'last_login'])->first();
        $old_updates = $old_updates['value'];

        $old_rate = UserConfigs::where(['app_id' => $this->securityContext()->getApp()->id, 'user_id' => $organizationId, 'key' => 'can_rate'])->first();
        $old_rate = $old_rate['value'];

        $rate = $this->request->get('rate');
        $updates = $this->request->get('updates');

        $sort_by = $this->request->get('sort_by');
        $order_by = $this->request->get('order_by');

        if($rate && $old_rate != $rate){
            $attributes = ['app_id' => $this->securityContext()->getApp()->id, 'key' => 'can_rate', 'user_id' => $organizationId];
            UserConfigs::updateOrCreate( $attributes, ['value' => $rate] );
        }
        else
            $rate = $old_rate;

        if($updates && $old_updates != $updates){
            $attributes = ['app_id' => $this->securityContext()->getApp()->id, 'key' => 'last_login', 'user_id' => $organizationId];
            UserConfigs::updateOrCreate( $attributes, ['value' => $updates] );
        }
        else
            $updates = $old_updates;

        $orgAppIds = $this->securityContext()->getToken()->currentUser()->appIds();
        if (in_array($this->securityContext()->getApp()->id, $orgAppIds)) {
            $results = $this->matchingResultsRepo()->matchingResults(BaseDataModel::ORGANIZATION, BaseDataModel::CANDIDATE, $organizationId, compact('sort_by', 'order_by'));

            if($updates < $old_updates) {

                $matched = false;
                if(!empty($results)){
                    foreach ($results as $result) {
                        $year = date('Y', strtotime($result['last_login']));
                        if ((int)$year <= $updates) {
                            $matched = true;
                            break;
                        }
                    }
                }

                if(!$matched) {
                    $data = ['userId' => $organizationId, 'appId' => $this->securityContext()->getApp()->id, 'last_login' => $updates];
                    Queue::push(OrgCandidatesQueue::class, $data);
                    return new RestResponse("rematch");
                }
            }

            return new RestResponse($this->matchingResultsRepo()->matchingResults(BaseDataModel::ORGANIZATION, BaseDataModel::CANDIDATE, $organizationId, compact('rate', 'updates', 'sort_by', 'order_by')));
        }
        return new RestResponse([]);
    }

    /**
     * @param $organizationId
     *
     * @return RestResponse
     */
    public function getLive($organizationId)
    {

        $this->securityContext()->getToken()->setRoles([BaseDataModel::ORGANIZATION]);

        $configRepo = $this->matchingConfigRepo()->setSecurityContext($this->securityContext());
        $config = $configRepo->configByOrganizationCandidates()->get();


        if (!$config->count()) {

            return new RestResponse([]);
        }

        $orgData = (new UserCachedRepository(BaseDataModel::ORGANIZATION))->mainData($organizationId)->getData();


        $canData = (new UserCachedRepository(BaseDataModel::CANDIDATE))->dataWithNotes($organizationId)->get()->toArray();

        $Matching = new OrganizationCandidates($orgData, $canData, $config->toArray());

        $canData = $Matching->matchResults();

        return new RestResponse($canData);
    }

}
