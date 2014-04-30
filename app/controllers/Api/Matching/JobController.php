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
use MissionNext\Repos\Field\Field;
use MissionNext\Api\Service\Matching\CandidateJobs;

class JobController extends BaseController
{

    public function getIndex($candidate_id)
    {

        $this->securityContext()->getToken()->setRoles([BaseDataModel::JOB]);

        $configRepo = $this->matchingConfigRepo()->setSecurityContext($this->securityContext());
        $config = $configRepo->configByCandidate(BaseDataModel::JOB, $candidate_id)->get();
        //  dd($this->getLogQueries());
        // dd($config->toArray());

        if (!$config->count()) {

            return new RestResponse([]);
        }

        $candidateData = DB::select("SELECT data FROM candidate_cached_profile WHERE id = ? ", [$candidate_id]);
        $folderNotesTable = (new FolderNotes)->getTable();

        if (empty($candidateData)) {

              return new RestResponse([]);
        }

            $candidateData = json_decode($candidateData[0]->data, true);

            $jobData = DB::select("SELECT jc.data, fN.notes, fN.folder FROM job_cached_profile as jc
             LEFT JOIN {$folderNotesTable} fN ON jc.id = fN.user_id
                  AND fN.for_user_id = ? AND fN.user_type = ?
            ", [$candidate_id, BaseDataModel::JOB]);


            $jobData = !empty($jobData) ? array_map(function ($d) {
                $data = json_decode($d->data, true);
                $data['notes'] = $d->notes;
                $data['folder'] = $d->folder;

                return $data;
            }, $jobData) : [];


            $Matching = new CandidateJobs($candidateData, $jobData, $config);

            $jobData = $Matching->matchResults();

            return new RestResponse(array_values($jobData));
    }

}






