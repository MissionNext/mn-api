<?php


namespace App\Modules\Api\MissionNext\Controllers\Folder;


use App\Modules\Api\Response\RestResponse;
use App\Modules\Api\MissionNext\Controllers\BaseController;
use App\Models\Folder\Folder;
use App\Models\FolderApps\FolderApps;

/**
 * Class FolderAppsController
 *
 * @package App\Modules\Api\MissionNext\Controllers\Folder
 */
class FolderAppsController extends BaseController
{
    /**
     * @return RestResponse
     */
    public function postIndex()
    {
        $request = $this->request->request;
        /** @var  $folderApps FolderApps */
        $folderApps = FolderApps::where("user_id", "=", $request->get("user_id"))
            ->where("for_user_id", "=", $request->get("for_user_id"))
            ->where("user_type", "=", $request->get("user_type"))
            ->where("app_id", "=", $this->securityContext()->getApp()->id)
            ->first();

        $folder = Folder::where("role", "=", $request->get('user_type'))
            ->where("app_id", "=", $this->securityContext()->getApp()->id)
            ->where("title", "=", $request->get("folder"))->first();

        if ($folder) {
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

        return new RestResponse(["error" => "Folder ".$request->get('folder')." not exist."]);
    }
}
