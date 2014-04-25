<?php

namespace MissionNext\Controllers\Api\Profile;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Api\Exceptions\SearchProfileException;
use MissionNext\Models\SearchData\SearchData;

class SearchController extends BaseController
{
    /**
     * @param $type
     * @return RestResponse
     * @throws \MissionNext\Api\Exceptions\SearchProfileException
     */
    public function postIndex($type)
    {
        $profileSearch = $this->request->get("profileData");
        $bindings = [];
        $tableName = $type.'_cached_profile';
        $query = "SELECT * FROM {$tableName}  ";
        $where = " WHERE ( ";
        if (!empty($profileSearch)) {
            $expandedFields = $this->viewFieldRepo()->getModel()->whereRaw("CAST(meta->'search_options'->>'is_expanded' AS BOOLEAN) = true")->get()->toArray();
            if (count($expandedFields)) {
                $expandedFields = array_fetch($expandedFields, 'symbol_key');
            }
          //  dd($expandedFields);
           //  dd($profileSearch);
            foreach ($profileSearch as $fieldName => $value) {

                if (is_array($value)) {
                    if (in_array($fieldName, $expandedFields)) {
                       // dd($fieldName);
                        foreach ($value as $val) {
                            $query .= $where . " ? = data->'profileData'->>'{$fieldName}' ";
                            $bindings[] = $val;
                            $where = " OR ";
                        }
                    } else {
                        $query .= $where . " ? && json_array_text(data->'profileData'->'{$fieldName}') ";
                        $bindings[] = addslashes(str_replace(["[", "]"], ["{", "}"], json_encode($value)));
                    }

                } else {
                    // var_dump($fieldName);
                    $query .= $where . " ? = data->'profileData'->>'{$fieldName}' ";
                    $bindings[] = $value;
                }

                $where = " AND ";
            }
        }
        $userSearch = $this->request->except("profileData", "timestamp");
        if (!empty($userSearch)) {

            foreach ($userSearch as $fN => $val) {
                $query .= $where . " ? = data->>'{$fN}' ";
                $bindings[] = $val;
            }
        }
        if (!empty($profileSearch) || !empty($userSearch)) {
            $query .= " ) ";
        }else{

            throw new SearchProfileException("No search params specified");
        }

        //dd($query, $bindings);

        return new RestResponse(array_map(function ($d) {

            return json_decode($d->data);
        }, DB::select($query, $bindings)));

    }


   public function postFor($search_type, $user_type, $user_id )
   {
       $search_data = $this->request->request->get("search_data");
      // dd(json_encode($search_data));
       $search_name = $this->request->request->get("search_name");
       $search = SearchData::create([
           "search_name"=> $search_name,
           "search_type"=>$search_type,
           "user_type"=>$user_type,
           "user_id"=>$user_id,
           "data" => json_encode($search_data)
       ]);


      return new RestResponse($search);

   }
}