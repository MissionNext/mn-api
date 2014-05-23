<?php


namespace MissionNext\Controllers\Api\Favorite;


use Illuminate\Support\Facades\DB;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\Favorite\Favorite;

class Controller extends BaseController {

    /**
     * @param $user_id
     * @param $role
     *
     * @return RestResponse
     */
    public function getByRole($user_id, $role){

        $cache_table = $role . "_cached_profile";

        $data = Favorite::where("app_id", '=', $this->securityContext()->getApp()->id())
            ->where("user_id", '=', $user_id)
            ->where("target_type", '=', $role)
            ->join($cache_table, $cache_table . ".id", "=", "favorite.target_id")
            ->select("favorite.id", "favorite.user_id", "favorite.target_type", "favorite.target_id" , "$cache_table.data as data")
            ->get();

        foreach($data as $key => $row){
            $data[$key]['data'] = json_decode($row['data']);
        }

        return new RestResponse($data);

    }

    /**
     * @return RestResponse
     */
    public function store(){

        $params = $this->request->request->all();
        $params['app_id'] = $this->securityContext()->getApp()->id();

        return new RestResponse(Favorite::create($params));
    }

    /**
     * @param $favorite_id
     *
     * @return RestResponse
     */
    public function delete($favorite_id){

        return new RestResponse(Favorite::where('id', '=', $favorite_id)->delete());
    }

} 