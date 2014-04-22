<?php
namespace MissionNext\Controllers\Api\Field;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use MissionNext\Api\Exceptions\ProfileException;
use MissionNext\Api\Response\RestResponse;
use Illuminate\Support\Facades\Input;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Repos\Field\FieldRepositoryInterface;

/**
 * Class Controller
 * @package MissionNext\Controllers\Api\Field
 */
class Controller extends BaseController
{

    /**
     * @param $type
     *
     * @return RestResponse
     */
    public function getIndex($type)
    {
  //SELECT * FROM json_each((SELECT b FROM aa WHERE a = 4)) WHERE key = 'f1';
//        $statement = DB::select("
//        SELECT data->'profileData'->>'alternate_speciality' FROM user_cached_profiles
//        WHERE data->'profileData'->>'alternate_speciality' <> ''
//        ");
//        $statement = DB::select("
//        SELECT * FROM json_each_text( ( SELECT data->'profileData' FROM  user_cached_profile WHERE user_id = 3  ) )
//
//        ");

//        $statement = DB::select("
//        SELECT json_text(json_array_elements(CAST(data->'profileData'->>'alternate_speciality' AS JSON)))  as alik  FROM  user_cached_profile WHERE user_id = 3
//
//        ");
                $statement = DB::select("
        SELECT * FROM (( SELECT json_text(json_array_elements(CAST(data->'profileData'->>'alternate_speciality' AS JSON)))  as alik FROM user_cached_profile WHERE user_id = 3  )) as SDF
        WHERE alik in ('pm','driver')

        ");
        $statement = DB::select("
        SELECT * FROM (( SELECT json_text(json_array_elements((data->'profileData'->>'alternate_speciality')::json ))  as alik FROM user_cached_profile WHERE user_id = 3  )) as SDF
        WHERE alik in ('pm','driver')

        ");

//        DB::statement("
//        CREATE OR REPLACE FUNCTION json_array_text(_j json)
//            RETURNS text[] AS
//            $$
//            SELECT array_agg(elem::text)
//            FROM json_array_elements(_j) AS elem
//            $$
//            LANGUAGE sql IMMUTABLE
//        ");

        $statement = DB::select("
        SELECT * FROM user_cached_profile  where ? <@  json_array_text(data->'profileData'->'favourite_movies')

        ", [addslashes('{"Buration"}')]);



     //  dd();
       // dd(json_encode();
//        $statement = DB::select("
//        SELECT json_array_text(data->'profileData'->'alternate_speciality')  FROM user_cached_profile  where user_id=3
//
//        ");
//        $statement = DB::select("
//        SELECT  data->'profileData'->>'alternate_speciality' FROM user_cached_profile
//
//        ");
      //  dd($statement);

        return new RestResponse($this->fieldRepo()->fieldsExpanded()->get());
    }

    /**
     * @param $type
     *
     * @return RestResponse
     */
    public function postIndex($type)
    {
        /** @var  $request \Symfony\Component\HttpFoundation\Request */
        $request = Request::instance()->request;
        $fields = $request->has("fields") ? $request->get("fields") : [];

        return new RestResponse($this->fieldRepo()->addFields($fields));
    }

    /**
     * @param $type
     *
     * @return RestResponse
     */
    public function putIndex($type)
    {
        /** @var  $request \Symfony\Component\HttpFoundation\Request */
        $request = Request::instance()->request;

        $fields = $request->has("fields") ? $request->get("fields") : [];

        return new RestResponse($this->fieldRepo()->updateFields($fields));
    }

    /**
     * @param $type
     *
     * @return RestResponse
     */
    public function deleteIndex($type)
    {
        /** @var  $request \Symfony\Component\HttpFoundation\Request */
        $query = Request::instance()->query;

        $ids = $query->has("ids") ? $query->get("ids") : [];

        return new RestResponse($this->fieldRepo()->deleteFields($ids));
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
        $fields = Input::get("fields", []);
        $sync = [];
        foreach ($fields as $field) {
            $sync[$field["id"]] = ["constraints" => $field["constraints"]];
        }

        $mFields = $this->fieldRepo()->modelFields();

        $idsBeforSync = $mFields->getRelatedIds();

        count($fields)
            ? $mFields->sync($sync)
            : $mFields->detach();

        $idsAfterSync = $mFields->getRelatedIds();

        $viewIdsToRemove = array_diff($idsBeforSync, $idsAfterSync);

        if (!empty($viewIdsToRemove)) {
            $symbol_keys = $this->fieldRepo()->getModel()->whereIn('id', $viewIdsToRemove)->lists('symbol_key');
            $this->viewFieldRepo()->deleteByDMSymbolKeys($this->getApp()->DM(), $symbol_keys);
        }

        return new RestResponse($mFields->get());
    }

}