<?php


namespace App\Modules\Api\MissionNext\Controllers\Translation;


use App\Modules\Api\Response\RestResponse;
use App\Modules\Api\MissionNext\Controllers\BaseController;
use App\Models\Language\LanguageModel;

/**
 * Class LanguageController
 * @package App\Modules\Api\Controllers\Translation
 */
class LanguageController extends BaseController
{
    /**
     * @return RestResponse
     */
    public function getIndex()
    {

        return new RestResponse(LanguageModel::all());
    }

    /**
     * @return RestResponse
     */
    public function postApplication()
    {
        $languages = $this->request->request->get('languages');
        $this->getApp()->languages()->sync($languages ?: []);

        return new RestResponse($this->getApp()->languages);
    }

    /**
     * @return RestResponse
     */
    public function getApplication()
    {

        return new RestResponse($this->getApp()->languages);
    }
}
