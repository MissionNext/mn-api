<?php

namespace MissionNext\Controllers\Api\Profile;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Api\Exceptions\SearchProfileException;

class SearchController extends BaseController
{
    public function postIndex($type)
    {
        $profileSearch = $this->request->get("profileData");
        $bindings = [];
        $query = "SELECT * FROM user_cached_profile";
        $where = " WHERE ";
        if (!empty($profileSearch)) {
            foreach ($profileSearch as $fieldName => $value) {

                if (is_array($value)) {
                    $query .= $where . " ? <@ json_array_text(data->'profileData'->'{$fieldName}') ";
                    $bindings[] = addslashes(str_replace(["[", "]"], ["{", "}"], json_encode($value)));
                } else {
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
            $query .= " AND type = ?";
            $bindings[] = $this->securityContext()->role();
        } else {
           throw new SearchProfileException("No search params specified");
        }
        //dd($query, $bindings);
        return new RestResponse(array_map(function($d){
             return json_decode($d);
        },array_fetch(DB::select($query, $bindings), 'data')));

    }
}