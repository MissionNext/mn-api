<?php

namespace MissionNext\Controllers\Admin\Subscription\Ajax;


use Illuminate\Support\Facades\Response;
use MissionNext\Controllers\Admin\AdminBaseController;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Subscription\Partnership;
use MissionNext\Models\Subscription\SubConfig;

class SubConfigController extends AdminBaseController
{
    const ROUTE_PREFIX = 'ajax.sub.config';

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getIndex()
    {
        $appId = $this->request->query->get('app');
        $configs = SubConfig::whereAppId( $appId )->get();
        if (!$configs->count()){

            return Response::json([ "config" => SubConfig::defConfig() ]);
        }

        $return = [];
        foreach($configs as  $config){
            $return[$config->role]['role'] = [ 'key' =>$config->role, 'label' => BaseDataModel::label($config->role) ];
            $return[$config->role]['partnership'][] =
                ["price_month" => intval($config->price_month), "level" =>$config->partnership,  "price_year" =>  intval($config->price_year)];

        }
        $conf = [];
        $conf[] = $return[BaseDataModel::ORGANIZATION];
        $partnership = $conf[0]['partnership'];
        $conf[0]['partnership'][0] = current(array_filter($partnership, function($p){
          return $p['level'] === 'limited';
        }));

        $conf[0]['partnership'][1] = current(array_filter($partnership, function($p){
            return $p['level'] === 'basic';
        }));
        $conf[0]['partnership'][2] = current(array_filter($partnership, function($p){
            return $p['level'] === 'plus';
        }));
        $conf[] = $return[BaseDataModel::AGENCY];
        $conf[] = $return[BaseDataModel::CANDIDATE];



        return Response::json([ "config" => $conf ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function postIndex()
    {
        $configs = $this->request->request->get('configs');
        $appId = $this->request->request->get('app');

        foreach($configs as $config){
           foreach($config['partnership'] as $p ) {
               SubConfig::updateOrCreate([
                   'app_id' => $appId,
                   'partnership' => $p['level'],
                   'role' => $config['role']['key']
               ],[
                   "price_month" => $p['price_month'],
                   "price_year" => $p['price_year'],
               ]);
           }
        }

        return Response::json(SubConfig::whereAppId( $appId )->get()->toArray());
    }

} 