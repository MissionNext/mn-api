<?php
namespace Api\Field;

use Api\BaseController;
use MissionNext\Api\Response\RestResponse;
use Illuminate\Support\Facades\Input;
use MissionNext\Models\Field\FieldFactory;

/**
 * Class Controller
 * @package Api\Profile
 * @description Field Controller
 */
class Controller extends BaseController {

    /**
     * @param $type
     *
     * @return RestResponse
     */
    public function getIndex($type)
	{

        return new RestResponse($this->fieldsChoicesArr(FieldFactory::roleBasedModel()->fieldsExp()->get()));
	}

    /**
     * @param $type
     *
     * @return RestResponse
     */
    public function getModel($type)
    {
        $application = $this->getApp();
        dd($application->dataModels()->first()->agencyFields());

        return new RestResponse($this->fieldsChoicesArr($application->modelFieldsExp()->get()));
    }


    /**
     * @param $type
     *
     * @return RestResponse
     */
    public function postModel($type)
    {
        $fields =  Input::get("fields", []);

        $application = $this->getApp();
        $mFields = $application->modelFields();

        count($fields)
            ? $mFields->sync($fields)
            : $mFields->detach();

        return new RestResponse($mFields->get());
    }

}