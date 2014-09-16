<?php

namespace MissionNext\Controllers\Api\AppConfig;


use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\Configs\UserConfigs;

/**
 * Class UserConfigController
 *
 * @package MissionNext\Controllers\Api\AppConfig
 */
class UserConfigController extends BaseController
{
    /**
     * Create User Config
     *
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
     * Get Config by userId
     *
     * @param integer $userId
     *
     * @return RestResponse
     */
    public function getIndex($userId)
    {

        return new RestResponse(UserConfigs::where(['app_id' => $this->getApp()->id(), 'user_id' => $userId])->get());
    }

    /**
     * Get Config by current user
     *
     * @return RestResponse
     */
    public function getCurrent()
    {

        return new RestResponse(UserConfigs::where(['app_id' => $this->getApp()->id(), 'user_id' => $this->getUser()->id])->get());
    }

    /**
     * Get User Config by userId and key
     *
     * @param string $key
     * @param integer $userId
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