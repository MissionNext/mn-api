<?php

namespace MissionNext\Controllers\Api\AppConfig;


use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\Configs\UserConfigs;

class UserConfigController extends BaseController
{
    /**
     * @return RestResponse
     */
    public function postIndex()
    {
        $key = $this->request->request->get('key');
        $value = $this->request->request->get('value');

        $attributes = ['app_id' => $this->getApp()->id(), 'key' => $key, 'user_id' => $this->getUser()->id];

        UserConfigs::updateOrCreate( $attributes, ['value' => $value] );

        return new RestResponse(true);
    }

    /**
     * @return RestResponse
     */
    public function getIndex($userId)
    {

        return new RestResponse(UserConfigs::where(['app_id' => $this->getApp()->id(), 'user_id' => $userId])->get());
    }

    /**
     * @return RestResponse
     */
    public function getCurrent()
    {

        return new RestResponse(UserConfigs::where(['app_id' => $this->getApp()->id(), 'user_id' => $this->getUser()->id])->get());
    }

    /**
     * @param $key
     *
     * @return RestResponse
     */
    public function getKey($key, $userId)
    {


        return new RestResponse(
            UserConfigs::where(
                [
                    'key' => $key,
                    'app_id' => $this->getApp()->id(),
                    'user_id' => $userId,
                ]
            )->first()
        );
    }
} 