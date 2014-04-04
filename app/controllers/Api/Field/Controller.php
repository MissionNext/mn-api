<?php
namespace Api\Field;

use Api\BaseController;
use MissionNext\Api\Response\RestResponse;
use Illuminate\Support\Facades\Input;
use MissionNext\Models\Field\FieldFactory;
use MissionNext\Repos\Field\FieldRepository;

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

        return new RestResponse($this->fieldRepo()->fieldsExpanded()->get());
	}

    /**
     * @param $type
     *
     * @return RestResponse
     */
    public function getModel($type)
    {

        return new RestResponse($this->fieldRepo()->modelFieldsExpanded()->get());

    }


    /**
     * @param $type
     *
     * @return RestResponse
     */
    public function postModel($type)
    {
        $fields =  Input::get("fields", []);
        $sync = [];
        foreach($fields as $field){
            $sync[$field["id"]] = ["constraints"=>$field["constraints"]];
        }

        $mFields = $this->fieldRepo()->modelFields();

        count($fields)
            ? $mFields->sync($sync)
            : $mFields->detach();

        return new RestResponse($mFields->get());
    }

}