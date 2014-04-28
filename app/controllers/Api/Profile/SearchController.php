<?php

namespace MissionNext\Controllers\Api\Profile;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Api\Exceptions\SearchProfileException;
use MissionNext\Facade\SecurityContext;
use MissionNext\Models\Field\Candidate;
use MissionNext\Models\Field\FieldType;
use MissionNext\Models\SearchData\SearchData;
use MissionNext\Repos\Field\Field;

class SearchController extends BaseController
{
    /**
     * @param $searchType
     *
     * @return RestResponse
     *
     * @throws \MissionNext\Api\Exceptions\SearchProfileException
     */
    public function search($searchType)
    {
        SecurityContext::getInstance()->getToken()->setRoles([$searchType]);

        $profileSearch = $this->request->get("profileData");
        $userSearch = $this->request->except("profileData", "timestamp");
     //   dd($profileSearch);

        $bindings = [];
        $tableName = $searchType.'_cached_profile';
        $query = "SELECT * FROM {$tableName}  ";
        $where = " WHERE ( ";
        if (!empty($profileSearch)) {

            $expandedFields = $this->viewFieldRepo()->getModel()->whereRaw("CAST(meta->'search_options'->>'is_expanded' AS BOOLEAN) = true")->get()->toArray();

            $currentField =  Field::currentFieldModelName(SecurityContext::getInstance());

            $inputFields =
                array_fetch(
                     (new $currentField)->where("type","=",FieldType::INPUT)->orWhere("type","=",FieldType::TEXT)->get()->toArray(),
                      "symbol_key"
            );


            if (count($expandedFields)) {
                $expandedFields = array_fetch($expandedFields, 'symbol_key');
            }
          //  dd($expandedFields);
           //  dd($profileSearch);
            foreach ($profileSearch as $fieldName => $value) {

                if (is_array($value)) {
                    if (in_array($fieldName, $expandedFields)) {
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
                    if (in_array($fieldName,$inputFields)){
                        $query .= $where . " data->'profileData'->>'{$fieldName}' LIKE ?  ";
                        $bindings[] = '%'.$value.'%';
                    }else {
                        $query .= $where . " ? = data->'profileData'->>'{$fieldName}' ";
                        $bindings[] = $value;
                    }
                }

                $where = " AND ";
            }
        }

        if (!empty($userSearch)) {

            foreach ($userSearch as $fN => $val) {
                $query .= $where . "  data->>'{$fN}'  LIKE ? ";
                $bindings[] = '%'.$val.'%';
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

    /**
     * @param $searchType
     * @param $userType
     * @param $userId
     *
     * @return RestResponse
     */
    public function postIndex($searchType, $userType, $userId )
    {
       $searchData = $this->request->request->get("search_data");
      // dd(json_encode($search_data));
       $searchName = $this->request->request->get("search_name");
       $search = SearchData::create([
           "search_name"=> $searchName,
           "search_type"=>$searchType,
           "user_type"=>$userType,
           "user_id"=>$userId,
           "data" => json_encode($searchData)
       ]);


      return new RestResponse($search);

    }

    /**
     * @param $searchType
     * @param $userType
     * @param $userId
     *
     * @return RestResponse
     */
    public function getIndex($searchType, $userType, $userId )
    {
        $data = SearchData::where("search_type","=",$searchType)
                   ->where("user_type", "=", $userType)
                   ->where("user_id", "=", $userId)
                   ->get();

        $data->each(function(&$d){
            $d->data = json_decode($d->data);
        });

        return new RestResponse($data);
    }

    /**
     * @param $searchId
     *
     * @return RestResponse
     */
    public function delete( $searchId )
    {

        return new RestResponse(SearchData::destroy($searchId));
    }
}