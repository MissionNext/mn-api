<?php


namespace MissionNext\Controllers\Api\Favorite;


use Illuminate\Support\Facades\DB;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Favorite\Favorite;
use MissionNext\Models\Notes\Notes;
use MissionNext\Models\User\User;

/**
 * Class Favourite Controller
 *
 * @package MissionNext\Controllers\Api\Favorite
 */
class Controller extends BaseController {

    /**
     * Get User Favourites by role and userId
     *
     * @param integer $user_id
     * @param string $role
     *
     * @return RestResponse
     */
    public function getByRole($user_id, $role){

        $cache_table = $role . "_cached_profile";
        $folderNotesTable = (new Notes)->getTable();
        $query = Favorite::join($cache_table, $cache_table . ".id", "=", "favorite.target_id")
                          ->select("favorite.id", "favorite.user_id", "favorite.target_type", "favorite.target_id" , "$cache_table.data as data", $folderNotesTable.".notes as notes");

        if ($role === BaseDataModel::JOB) {
            $query = $query->leftJoin("organization_cached_profile",
                "organization_cached_profile.id", "=", DB::raw("(job_cached_profile.data->>'organization_id')::int"))
                ->addSelect( "organization_cached_profile.data as organization" );
        }

        $data = $query->where("app_id", '=', $this->securityContext()->getApp()->id())
            ->where("favorite.user_id", '=', $user_id)
            ->where("favorite.target_type", '=', $role)

            ->leftJoin($folderNotesTable, function($join) use ($folderNotesTable, $user_id){
                $join->on($folderNotesTable . ".user_id", '=', 'favorite.target_id');
                $join->on($folderNotesTable . ".for_user_id", '=', DB::raw($user_id));
            })
            ->get();

        foreach($data as $key => $row){
            if ($role === BaseDataModel::JOB) {
                $data[$key]['data'] = json_decode($row['data']);

                $data[$key]['data']->organization = json_decode($row['organization']);
                unset($data[$key]['organization']);
            } elseif (User::find($row['target_id'])->isActiveInApp($this->securityContext()->getApp())) {
                $data[$key]['data'] = json_decode($row['data']);
            } else {
                unset($data[$key]);
            }

        }

        return new RestResponse($data);
    }

    /**
     * Save Favourite
     *
     * @return RestResponse
     */
    public function store(){

        $params = $this->request->request->all();
        $params['app_id'] = $this->securityContext()->getApp()->id();

        return new RestResponse(Favorite::create($params));
    }

    /**
     * Delete Favourite
     *
     * @param integer $favorite_id
     *
     * @return RestResponse
     */
    public function delete($favorite_id){

        return new RestResponse(Favorite::where('id', '=', $favorite_id)->delete());
    }

} 