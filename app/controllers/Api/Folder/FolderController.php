<?php

namespace MissionNext\Controllers\Api\Folder;


use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
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
     */
    public function store()
    {

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


} 