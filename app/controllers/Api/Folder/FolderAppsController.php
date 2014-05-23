<?php


namespace MissionNext\Controllers\Api\Folder;


use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\FolderApps\FolderApps;

class FolderAppsController extends BaseController
{
    public function postIndex()
    {
        $request = $this->request->request;
        /** @var  $folderApps FolderApps */
        $folderApps = FolderApps::where("user_id", "=", $request->get("user_id"))
            ->where("for_user_id", "=", $request->get("for_user_id"))
            ->where("user_type", "=", $request->get("user_type"))
            ->where("app_id", "=", $this->securityContext()->getApp()->id)
            ->first();

        if ($folderApps) {
            $folderApps->setFolder($request->get("folder"));
            $folderApps->save();
        } else {
            $data = $request->all();
            $data["app_id"] = $this->securityContext()->getApp()->id;
            $folderApps = FolderApps::create($data);
        }

        return new RestResponse($folderApps);
    }
} 