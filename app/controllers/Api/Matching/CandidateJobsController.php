<?php


namespace MissionNext\Controllers\Api\Matching;


use Illuminate\Support\Facades\DB;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Facade\SecurityContext;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Field\FieldType;
use MissionNext\Models\FolderNotes\FolderNotes;
use MissionNext\Models\Matching\Config;
use MissionNext\Repos\CachedData\UserCachedRepository;
use MissionNext\Repos\Field\Field;
use MissionNext\Api\Service\Matching\CandidateJobs;

class CandidateJobsController extends BaseController
{
    /**
     * @param $candidate_id
     *
     * @return RestResponse
     */
    public function getIndex($candidate_id)
    {

        $this->securityContext()->getToken()->setRoles([BaseDataModel::JOB]);

        $configRepo = $this->matchingConfigRepo()->setSecurityContext($this->securityContext());
        $config = $configRepo->configByCandidateJobs(BaseDataModel::JOB, $candidate_id)->get();

        if (!$config->count()) {

            return new RestResponse([]);
        }
       // dd($config->toArray());
        $candidateData = (new UserCachedRepository(BaseDataModel::CANDIDATE))->select('data')->findOrFail($candidate_id);

        if (empty($candidateData)) {

              return new RestResponse([]);
        }

        $candidateData = json_decode($candidateData->data, true);

        $jobData = (new UserCachedRepository(BaseDataModel::JOB))->dataWithNotes($candidate_id)->get();

        $jobData = !empty($jobData) ? array_map(function ($d) {
            $data = json_decode($d->data, true);
            $data['notes'] = $d->notes;
            $data['folder'] = $d->folder;

            return $data;
        }, $jobData) : [];

        $Matching = new CandidateJobs($candidateData, $jobData, $config);

        $jobData = $Matching->matchResults();

        return new RestResponse($jobData);
    }

}






