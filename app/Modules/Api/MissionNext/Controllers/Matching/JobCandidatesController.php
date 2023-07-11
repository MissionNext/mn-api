<?php

namespace App\Modules\Api\MissionNext\Controllers\Matching;

use App\Modules\Api\Response\RestResponse;
use App\Modules\Api\Service\Matching\JobCandidates;
use App\Modules\Api\MissionNext\Controllers\BaseController;
use App\Models\Configs\UserConfigs;
use App\Models\DataModel\BaseDataModel;
use App\Repos\CachedData\UserCachedRepository;

/**
 * Class JobCandidatesController
 * @package App\Modules\Api\Controllers\Matching
 */
class JobCandidatesController extends BaseController
{
    /**
     *
     * @param $jobId
     *
     * @return RestResponse
     */
    public function getIndex($jobId)
    {
        $user_id = $this->request->get('user_id');
        $job_owner = $this->request->get('job_owner');
        $sort_by = $this->request->get('sort_by');
        $order_by = $this->request->get('order_by');

        $old_rate = UserConfigs::where(['app_id' => $this->securityContext()->getApp()->id, 'user_id' => $user_id, 'key' => 'can_job_rate'])->first();
        $old_rate = $old_rate['value'];

        $rate = $this->request->get('rate');

        if($rate && $old_rate != $rate){
            $attributes = ['app_id' => $this->securityContext()->getApp()->id, 'key' => 'can_job_rate', 'user_id' => $user_id];
            UserConfigs::updateOrCreate( $attributes, ['value' => $rate] );
        }
        else
            $rate = $old_rate;

        $userAppsIds = $this->securityContext()->getToken()->currentUser()->appIds();

        if (in_array($this->securityContext()->getApp()->id, $userAppsIds)) {

            return
                new RestResponse($this->matchingResultsRepo()
                    ->matchingResults(BaseDataModel::JOB, BaseDataModel::CANDIDATE, $jobId, compact('rate', 'job_owner', 'sort_by', 'order_by', 'user_id')));
        }

        return new RestResponse([]);
    }

    public function getLive($jobId)
    {
        $this->securityContext()->getToken()->setRoles([BaseDataModel::JOB]);

        $configRepo = $this->matchingConfigRepo()->setSecurityContext($this->securityContext());
        $config = $configRepo->configByJobCandidates()->get();

        if (!$config->count()) {

            return new RestResponse([]);
        }
        $jobData = (new UserCachedRepository(BaseDataModel::JOB))->mainData($jobId)->getData();

        //TODO dataWithNotes set owner(organization) id
        $candidateData = (new UserCachedRepository(BaseDataModel::CANDIDATE))->dataWithNotes()->get()->toArray();


        $Matching = new JobCandidates($jobData, $candidateData, $config->toArray());

        $candidateData = $Matching->matchResults();

        return new RestResponse($candidateData);
    }
}
