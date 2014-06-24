<?php


namespace MissionNext\Controllers\Api\AppConfig;


use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\Configs\AppConfigs;
use MissionNext\Models\User\User;

class ConfigController extends BaseController
{
    /**
     * @return RestResponse
     */
    public function postIndex()
    {
        $key = $this->request->request->get('key');
        $value = $this->request->request->get('value');

        $attributes = ['app_id' => $this->getApp()->id(), 'key' => $key];
        $values = array_merge($attributes, ['value' => $value]);
        AppConfigs::updateOrCreate( $attributes, $values );

        return new RestResponse(true);
    }

    /**
     * @return RestResponse
     */
    public function getIndex()
    {

        return new RestResponse($this->getApp()->configs);
    }

    /**
     * @param $key
     *
     * @return RestResponse
     */
    public function getKey($key)
    {

        return new RestResponse($this->getApp()->configs()->whereKey($key)->get());
    }



} 