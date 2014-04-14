<?php

namespace MissionNext\Controllers\Api;

use MissionNext\Controllers\Api\BaseController;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use MissionNext\Api\Exceptions\ValidationException;
use MissionNext\Api\Response\RestResponse;
use Illuminate\Support\Facades\Request;
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

        return new RestResponse($this->jobRepo()->all());
    }


    /**
     * @return RestResponse
     * @throws \MissionNext\Api\Exceptions\UserException
     * @throws \MissionNext\Api\Exceptions\ValidationException
     */
    public function store()
    {
        $validationData = [ "name" => Input::get("name"),
            "symbol_key" =>  Input::get("symbol_key"),

        ];

        $constraints = [
            "name" => "required|between:3,100",
            "symbol_key" => "required|unique:jobs,symbol_key",
        ];
        /** @var  $validator \Illuminate\Validation\Validator */
        $validator = Validator::make(
            $validationData,
            $constraints
        );
        if ($validator->fails())
        {
            throw new ValidationException($validator->messages());
        }

        /** @var  $req \Symfony\Component\HttpFoundation\Request */
        $profileData = Input::except("timestamp","name","symbol_key");

        $jobRepo = $this->jobRepo();
        $job = $jobRepo->getModel();
        $job->setName(Input::get("name"))
            ->setSymbolKey(Input::get("symbol_key"));

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

        return new RestResponse($this->jobRepo()->find($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     *
     * @return RestResponse
     */
    public function update($id)
    {
        $user = $this->userRepo()->find($id);
        $data = Request::only(["name", "symbol_key"]);
        $filteredData = array_filter($data);
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
        $searchByData = Request::only(["name", "symbol_key"]);
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