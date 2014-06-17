<?php


namespace MissionNext\Controllers\Api\Translation;

use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\Translation\FolderTrans;


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
            $folderTrans =  FolderTrans::firstOrNew(
                [
                    'lang_id' => $dataTrans->lang_id,
                    'folder_id' => $dataTrans->folder_id,
                    'app_id' => $dataTrans->app_id,

                ]);

            $folderTrans->value  ?   $folderTrans->updateTransData($dataTrans)
                :   $folderTrans->insertTransData($dataTrans);
        }

        return new RestResponse([$data]);
    }


    public function getFolderTrans($type)
    {

        return new RestResponse((new FolderTrans())->folderTrans($this->securityContext(), $type) );
    }
} 