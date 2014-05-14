<?php

namespace MissionNext\Controllers\Api\Folder;


use MissionNext\Api\Exceptions\SecurityContextException;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Filter\RouteSecurityFilter;
use MissionNext\Models\Folder\Folder;

class FolderController extends BaseController
{
    /**
     * @return RestResponse
     */
    public function index()
    {
        return new RestResponse(Folder::all());
    }

    /**
     * @return RestResponse
     *
     * @throws \MissionNext\Api\Exceptions\SecurityContextException
     */
    public function store()
    {
        if (!RouteSecurityFilter::isAllowedRole($this->request->request->get('role'))){

            throw new SecurityContextException("role doesn't exists");
        }

        return new RestResponse(Folder::create($this->request->request->all()));
    }

    /**
     * @param $id
     * @return RestResponse
     */
    public function show($id)
    {

        return new RestResponse(Folder::findOrFail($id));
    }

    /**
     * @param $id
     *
     * @return RestResponse
     */
    public function update($id)
    {
       $folder =  Folder::findOrFail($id);
       $folder->title = $this->request->request->get('title');
       $folder->save();

       return new RestResponse($folder);
    }

    /**
     * @param $id
     *
     * @return RestResponse
     */
    public function destroy($id)
    {
        $folder = Folder::findOrFail($id);
        $folder->delete();

        return new RestResponse($folder);
    }

    /**
     * @param $role
     *
     * @return RestResponse
     */
    public function role($role)
    {

        return new RestResponse(Folder::where("role","=",$role)->get());
    }




} 