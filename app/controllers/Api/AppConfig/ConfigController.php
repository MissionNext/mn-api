<?php


namespace MissionNext\Controllers\Api\AppConfig;


use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\Configs\AppConfigs;

class ConfigController extends BaseController
{
    /**
     * @return RestResponse
     */
    public function postIndex()
    {
        $configs = $this->request->request->get('configs');
        $configsModels = [];

        foreach($configs as $config){
            array_push($configsModels, new AppConfigs(['key'=> $config['key'], 'value' => $config['value'] ]));
        }
        $this->getApp()->configs()->saveMany($configsModels);

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