<?php

namespace App\Modules\Api\MissionNext\Controllers\Matching;

use App\Models\Matching\Config;
use App\Modules\Api\Response\RestResponse;
use App\Modules\Api\Service\Matching\Queue\Master\ConfigUpdateMatching;
use App\Modules\Api\MissionNext\Controllers\BaseController;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Input;
use Symfony\Component\HttpKernel\Controller\ErrorController;

/**
 * Class ConfigController
 * @package App\Modules\Api\Controllers\Matching
 */
class ConfigController extends BaseController
{
    /**
     * @param $type
     *
     * @return RestResponse
     */
    public function getIndex($type)
    {
        //$model = new Config();
        // $role = Route::input('type');
        // Cache::put('role',$role);
        //dd($this->matchingConfigRepo());
        //dd($this->matchingConfigRepo()->getModel());
        //dd($this->securityContext()->getToken());
        //dd($this->securityContext()->getApp()->id);
        //dd($this->securityContext());
        //$model = new \App\Models\Field\Organization;

        return new RestResponse($this->matchingConfigRepo()->where("app_id", '=', $this->securityContext()->getApp()->id)->with("mainField")->with("matchingField")->get());
        $item = [];
        $item = $this->matchingConfigRepo()->where("app_id", '=', $this->securityContext()->getApp()->id)->with("matchingField")->get();
       try{
            $item = $this->matchingConfigRepo()->where("app_id", '=', $this->securityContext()->getApp()->id)->with("mainField")->with("matchingField")->get();
       } catch (\Illuminate\Database\QueryException $e) {
            $error = new ErrorController();
            $error->alert($e->getMessage());
        }
       return new RestResponse($item);
    }

    /**
     * @param $type
     *
     * @return RestResponse
     */
    public function putIndex($type)
    {
        $model = $this->matchingConfigRepo()->getModel();
        $modelAppQuery = $model->where('app_id', '=', $this->getApp()->id());
        $modelAppQuery->delete();
        $configs = Input::get("configs");

        if (is_null($configs)){

            return new RestResponse([]);
        }
        $this->matchingConfigRepo()->insert($configs);

        $queueData = ["appId"=>$this->getApp()->id(), "role" => $this->securityContext()->role(), "userId" => 0];
        ConfigUpdateMatching::run($queueData);

        return new RestResponse( $modelAppQuery->with("mainField")->with('matchingField')->get() );
    }
}
