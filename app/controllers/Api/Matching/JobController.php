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

        if (!empty($candidateData)) {
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

            $configArr = $config->toArray();
            $maxMatching = 0;
            $config->each(function ($c) use (&$maxMatching) {
                $maxMatching += $c->weight;
            });

            SecurityContext::getInstance()->getToken()->setRoles([BaseDataModel::CANDIDATE]);
            $candidateField = Field::currentFieldModelName(SecurityContext::getInstance());
            $selectFieldTypes = [FieldType::SELECT, FieldType::SELECT_MULTIPLE, FieldType::CHECKBOX, FieldType::RADIO];
            $selectCanFields = array_fetch((new $candidateField)->whereIn("type",
                $selectFieldTypes)->get()->toArray(), 'symbol_key');
            SecurityContext::getInstance()->getToken()->setRoles([BaseDataModel::JOB]);
            $jobField = Field::currentFieldModelName(SecurityContext::getInstance());
            $selectJobFields = array_fetch((new $jobField)->whereIn("type",
                $selectFieldTypes)->get()->toArray(), 'symbol_key');

            //   dd($selectCanFields, $selectJobFields);


            //dd($configArr, $candidateData);
            $tempJobData = $jobData;


            foreach ($jobData as $k => $job) {
                foreach ($configArr as $conf) {
                    $jobKey = $conf['job_key'];
                    $candidateKey = $conf['candidate_key'];
                    $jobProfile = $job['profileData'];
                    $canProfile = $candidateData['profileData'];
                    if (isset($jobProfile[$jobKey]) && isset($canProfile[$candidateKey])) {

                        $jobValue = $jobProfile[$jobKey];
                        $canValue = $canProfile[$candidateKey];

                        if (!is_array($jobValue)) {  /** convert  all values to array to compare */

                            $jobValue = [$jobValue];
                        }
                        if (!is_array($canValue)) {

                            $canValue = [$canValue];
                        }
//                            if ($job['id'] == 3) {
//                                var_dump("job_key = $jobKey", "can_key = $candidateKey", "job_value =", $jobValue, "can_value=", $canValue, "weight = {$conf['weight']}");
//                            }
                        /** if value starts with (!) any value  matches */
                        if (in_array($jobKey, $selectJobFields)) {
                            foreach ($jobValue as $jV) {
                                if (starts_with($jV, '(!)')) {
                                    $jobData[$k]["profileData"][$jobKey] =
                                        ["job_value" => $jobValue, "candidate_value" => $canValue, "matches" => true, "weight" => $conf["weight"]];
                                    continue 2;
                                }
                            }
                        }
                        /** if value starts with (!) any value  matches */
                        if (in_array($candidateKey, $selectCanFields)) {
                            foreach ($canValue as $cV) {
                                if (starts_with($cV, '(!)')) {
                                    $jobData[$k]["profileData"][$jobKey] =
                                        ["job_value" => $jobValue, "candidate_value" => $canValue, "matches" => true, "weight" => $conf["weight"]];
                                    continue 2;
                                }
                            }
                        }


//                            array_walk($jobValue, function($val){
//                                dd($val);
//                            });
//                            var_dump($jobValue, $canValue);
                        /** if weight 5 (must match) and value doesn't matches remove add to banned ids */
                        if ($canValue !== $jobValue && $conf["weight"] == 5) {
                            unset($tempJobData[$k]);
                            continue;
                        }


                        if ($canValue !== $jobValue) {
                            $jobData[$k]["profileData"][$jobKey] =
                                ["job_value" => $jobValue, "candidate_value" => $canValue, "matches" => false, "weight" => $conf["weight"]];
                        }

                        if ($canValue === $jobValue) {
                            $jobData[$k]["profileData"][$jobKey] =
                                ["job_value" => $jobValue, "candidate_value" => $canValue, "matches" => true, "weight" => $conf["weight"]];
                        }


                    }elseif( !isset($jobProfile[$jobKey]) ){
                        /** if in profile data no symbol key that is in match config and weight is 5, remove  element from match */

                        if ($conf['weight'] == 5) {
                            unset($tempJobData[$k]);
                            continue;
                        } else {
                            $jobData[$k]["profileData"][$jobKey] =
                                ["job_value" => null, "candidate_value" => null, "matches" => false, "weight" => $conf["weight"]];
                        }
                    }
                }
            }

            $jobData = array_intersect_key($jobData, $tempJobData);

            foreach ($jobData as &$job) {
                $job['matching_percentage'] = 0;
                foreach ($job['profileData'] as $key=>&$prof) {
                    //  var_dump($prof);
                    if (isset($prof['matches']) && $prof['matches']) {
                        $job['matching_percentage'] += $prof['weight'];
                    } elseif (!isset($prof['matches'])) {
                        //dd($key);
                    //@TODO job field not in matching config
                        $prof = ["job_value" => $prof, "candidate_value" => null];
                    }
                }
                $job['matching_percentage'] = round(($job['matching_percentage'] / $maxMatching) * 100);
            }

            return new RestResponse(array_values($jobData));
        }


        return new RestResponse([]);
    }

}






