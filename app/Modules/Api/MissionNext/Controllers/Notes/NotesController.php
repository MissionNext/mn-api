<?php

namespace App\Modules\Api\MissionNext\Controllers\Notes;

use App\Modules\Api\Response\RestResponse;
use App\Modules\Api\MissionNext\Controllers\BaseController;
use App\Models\Notes\Notes;

/**
 * Class NotesController
 * @package App\Modules\Api\MissionNext\Controllers\Notes
 */
class NotesController extends BaseController
{
    public function postIndex()
    {
        $request = $this->request->request;
        /** @var  $notes Notes */
        $notes = Notes::where("user_id", "=", $request->get("user_id"))
            ->where("for_user_id", "=", $request->get("for_user_id"))
            ->where("user_type", "=", $request->get("user_type"))
            ->first();

        if ($notes) {
            $notes->setNotes($request->get('notes'));
            $notes->save();
        } else {
            $notes = Notes::create($this->request->request->all());
        }

        return new RestResponse($notes);
    }

}
