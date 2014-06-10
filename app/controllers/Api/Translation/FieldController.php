<?php


namespace MissionNext\Controllers\Api\Translation;


use MissionNext\Api\Response\RestResponse;
use MissionNext\Controllers\Api\BaseController;
use MissionNext\Models\Field\BaseField;
use MissionNext\Models\Field\Candidate;
use MissionNext\Models\Field\IField;
use MissionNext\Repos\Field\FieldRepository;
use MissionNext\Repos\Field\FieldRepositoryInterface;
use MissionNext\Repos\Translation\FieldRepository as TransFieldRepo;
use MissionNext\Repos\Translation\FieldRepositoryInterface as TransFieldRepoInterface;

class FieldController extends BaseController
{
    /**
     * @param $type
     *
     * @return RestResponse
     */
    public function postIndex($type)
    {
        $trans = $this->request->request->get('language');
        /** @var  $fieldRepo FieldRepository */
        $fieldRepo = $this->repoContainer[FieldRepositoryInterface::KEY];
        /** @var  $fieldModel IField */
        $fieldModel = $fieldRepo->getModel()->findOrFail($trans["field_id"]);
        $fieldModel->languages()->detach([$trans["lang_id"]]);
        $fieldModel->languages()->attach([$trans["lang_id"] => [ 'name' => $trans['label']] ]);

        return new RestResponse($fieldModel->languages);
    }

} 