<?php

namespace MissionNext\Controllers\Api;

use MissionNext\Controllers\Api\BaseController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use MissionNext\Api\Exceptions\ValidationException;
use MissionNext\Api\Response\RestResponse;
use Illuminate\Support\Facades\Request;
use MissionNext\Models\User\User;
use MissionNext\Validators\Job as JobValidator;

/**
 * Class JobController
 * @package MissionNext\Controllers\Api
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

        return new RestResponse($this->jobRepo()->getModel()->with('organization')->get());
    }


    /**
     * @return RestResponse
     * @throws \MissionNext\Api\Exceptions\UserException
     * @throws \MissionNext\Api\Exceptions\ValidationException
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
        /** @var  $organization User */
        $organization = $this->userRepo()->getModel()->findOrFail(Input::get('organization_id'));

        $jobRepo = $this->jobRepo();
        $job = $jobRepo->getModel();
        $job->setName(Input::get("name"))
            ->setSymbolKey(Input::get("symbol_key"))
            ->setOrganization($organization);

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

        return new RestResponse($this->jobRepo()->getModel()->with('organization')->find($id));
    }

    /**
     * @param $id
     *
     * @return RestResponse
     *
     * @throws \MissionNext\Api\Exceptions\ValidationException
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
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return RestResponse
     */
    public function destroy($id)
    {
        $user = $this->jobRepo()->find($id);
        $user->delete();

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

        return new RestResponse($this->jobRepo()->getModel()->whereRaw($str, $arrV)->get());
    }

} 