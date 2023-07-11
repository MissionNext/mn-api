<?php
namespace App\Modules\Api\MissionNext\Controllers\Translation;

use App\Modules\Api\Response\RestResponse;
use App\Modules\Api\MissionNext\Controllers\BaseController;
use App\Models\Translation\FolderTrans;

/**
 * Class FolderTransController
 * @package App\Modules\Api\MissionNext\Controllers\Translation
 */
class FolderTransController extends BaseController
{
    /**
     * @return RestResponse
     */
    public function postIndex()
    {
        $data = $this->request->request->all();
        foreach($data['folder'] as $transGroup)
        {
            $dataTrans = new \ArrayObject($transGroup, \ArrayObject::ARRAY_AS_PROPS);
            $dataTrans->offsetSet('app_id', $this->getApp()->id());
            /** @var  $folderTrans FolderTrans */
            $folderTrans =  FolderTrans::whereLangId($dataTrans->lang_id)
                                        ->whereAppId($dataTrans->app_id)
                                        ->whereFolderId($dataTrans->folder_id)
                                        ->first() ? : new FolderTrans();

            $folderTrans->app_id  ?   $folderTrans->updateTransData($dataTrans)
                :   $folderTrans->insertTransData($dataTrans);
        }

        return new RestResponse([$data]);
    }


    public function getFolderTrans($type)
    {

        return new RestResponse((new FolderTrans())->folderTrans($this->securityContext(), $type) );
    }
}
