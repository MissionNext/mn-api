<?php

namespace App\Modules\Api\MissionNext\Controllers\User;

use App\Modules\Api\MissionNext\Controllers\BaseController;
use App\Modules\Api\Exceptions\UserException;
use App\Modules\Api\Exceptions\ValidationException;
use App\Modules\Api\Response\RestResponse;
use Illuminate\Support\Facades\Request;
use App\Models\DataModel\BaseDataModel;
use App\Models\Matching\Results;
use App\Models\Observers\UserObserver;
use App\Models\User\User;
use App\Repos\CachedData\UserCachedRepository;
use App\Repos\CachedData\UserCachedRepositoryInterface;
use App\Modules\Api\Validators\Job as JobValidator;
use Input;
/**
 * Class JobController
 * @package App\Modules\Api\Controllers\User
 */
class JobController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return RestResponse
     */
    public function index()
    {
//        $jobs = Job::with('organization')->get();
//
//        $this->securityContext()->getToken()->setRoles([BaseDataModel::JOB]);
//        foreach($jobs as $job){
//            $this->updateUserProfile($job);
//        }

        return new RestResponse($this->jobRepo()->getModel()->with('organization')->get());
    }


    /**
     * @return RestResponse
     * @throws UserException
     * @throws ValidationException
     */
    public function store()
    {
        $jobValidator = new JobValidator(Request::instance());

        if (!$jobValidator->passes())
        {
            throw new ValidationException($jobValidator->getErrors());
        }

        /** @var  $req \Symfony\Component\HttpFoundation\Request */
        $profileData = Input::except("timestamp","name","symbol_key","organization_id");

        if ($files = Input::file()){
            $this->checkFile($files['profile'], $profileData);
        }

        /** @var  $organization User */
        $organization = $this->userRepo()->getModel()->findOrFail(Input::get('organization_id'));



        $jobRepo = $this->jobRepo();
        $job = $jobRepo->getModel();
        $job->setObserver(new UserObserver());
        $job->setName(Input::get("name"))
            ->setSymbolKey(Input::get("symbol_key"))
            ->setOrganization($organization)
            ->addApp($this->getApp());

        //@TODO CHECK if organization has current app_id to create job
        $this->updateUserProfile($job, $profileData);

        return new RestResponse($job);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return RestResponse
     */
    public function show($id)
    {

        /** @var  $cacheData UserCachedRepository */
        $cacheData = $this->repoContainer[UserCachedRepositoryInterface::KEY];
        $cacheData->findOrFail($id);

        return new RestResponse($cacheData->transData($this->getToken()->language()));    }

    /**
     * @param $id
     *
     * @return RestResponse
     *
     * @throws ValidationException
     */
    public function update($id)
    {
        $user = $this->jobRepo()->find($id);
        $data = Request::only(["name", "symbol_key", "organization_id"]);
        $filteredData = array_filter($data);
        $jobValidator = new JobValidator(Request::instance());
        if (!$jobValidator->passes()){

            throw new ValidationException($jobValidator->getErrors());
        }

        foreach ($filteredData as $prop => $val) {
            $user->$prop = $val;
        }
        $user->save();

        return new RestResponse($user);
    }

    /**
     * @param $id
     * @param $organizationId
     * @return RestResponse
     * @throws UserException
     */
    public function delete($id, $organizationId)
    {
        $old_login = Input::get('old_login');

        $user = $this->jobRepo()->find($id);
        if ($user->organization->id != $organizationId){
            throw new UserException("Can't delete job, owner invalid");
        }

        if ($old_login) {
            $this->logger('job', 'delete', "Admin $old_login deleted job with id $user->id");
        } else {
            $this->logger('job', 'delete', "User $organizationId deleted job with id $user->id");
        }

        $user->delete();
        Results::where('user_id', $user->id)->orWhere('for_user_id', $user->id)->delete();

        return new RestResponse($user);
    }

    /**
     * @return RestResponse
     */
    public function find()
    {
        $searchByData = Request::only(["name", "symbol_key", "organization_id"]);
        $searchByData = array_filter($searchByData);
        $str = '';
        $arrV = [];
        for ($c = count($searchByData), $i = 0; $i < $c; $i++) {
            $isAnd = $i !== ($c - 1) ? ' and ' : '';
            $str .= key($searchByData) . " = ?" . $isAnd;
            $arrV[] = current($searchByData);
            next($searchByData);
        }

        $str .= " and app_id = ?";
        $arrV[] = $this->securityContext()->getApp()->id();

        $jobsId = $this->jobRepo()->getModel()->whereRaw($str, $arrV)->pluck('id');
        $jobs = [];
        foreach ($jobsId as $jobId) {
            $jobCache = (new UserCachedRepository(BaseDataModel::JOB))->where('id', $jobId)->get();
            $jobs[] = json_decode($jobCache[0]['data']);
        }

        return new RestResponse($jobs);
    }

    /**
     * @param $organizationId
     * @return RestResponse
     */
    public function findByOrgId($organizationId){
        $jobs = $this->jobRepo()->getModel()
                        ->where('organization_id', $organizationId)
                        ->where('app_id', $this->securityContext()->getApp()->id())->get();

        $new_output = $output = [];
        foreach($jobs as $job){
            $jobCache = (new UserCachedRepository(BaseDataModel::JOB))->where('id', $job['id'])->get();
            if (isset($jobCache[0])) {
                $output[] = json_decode($jobCache[0]['data']);
            }
        }

        if (count($output) > 0) {
            usort($output, function($a, $b) {
                return strcasecmp($a->name, $b->name);
            });

            /* second level sorting */
            $sorted_array = [];
            foreach ($output as $value) {
                $sorted_array[$value->name][] = $value;
            }
            foreach ($sorted_array as &$title_array) {
                for ($i = 0, $iMax = count($title_array); $i < $iMax; $i++) {
                    if (!isset($title_array[$i]->profileData->second_title)) {
                        $title_array[$i]->profileData->second_title = '';
                    }
                }

                usort($title_array, function ($a, $b) {
                    return strcasecmp($a->profileData->second_title, $b->profileData->second_title);
                });
            }

            $new_output = [];
            foreach ($sorted_array as $title_array_item) {
                foreach($title_array_item as $array_item) {
                    $new_output[] = $array_item;
                }
            }
        }


        return new RestResponse($new_output);
    }
}
