<?php

namespace MissionNext\Controllers\Api\Matching;


use Illuminate\Support\Facades\Input;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Facade\SecurityContext;
use MissionNext\Models\Field\Candidate;
use MissionNext\Models\Matching\Config;
use MissionNext\Models\Field\Candidate as CandidateFieldModel;

class ConfigController extends BaseController
{
    /**
     * @param $type
     *
     * @return RestResponse
     */
    public function getIndex($type)
    {

        return new RestResponse($this->matchingConfigRepo()->with("mainField")->with("matchingField")->get());
    }

    /**
     * @param $type
     *
     * @return RestResponse
     */
    public function putIndex($type)
    {
        $model = $this->matchingConfigRepo()->getModel();
        $modelAppQuery = $model->where('app_id', '=', $this->getApp()->id);
        $modelAppQuery->delete();
        $configs = Input::get("configs");
        $this->matchingConfigRepo()->insert($configs);

        return new RestResponse( $modelAppQuery->with("mainField")->with('matchingField')->get() );
    }
} 