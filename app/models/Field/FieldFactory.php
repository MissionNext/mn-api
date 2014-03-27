<?php
namespace MissionNext\Models\Field;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use MissionNext\Facade\SecurityContext;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\ModelInterface;

class FieldFactory
{

    /**
     * @return BaseField
     *
     * @throws \Exception
     */
    public static function roleBasedModel()
    {
        $role = SecurityContext::role();
        $model = null;

        switch ($role) {
            case BaseDataModel::CANDIDATE:
                $model = new Candidate();
                break;
            case BaseDataModel::AGENCY:
                $model = new Agency();
                break;
            case BaseDataModel::ORGANIZATION:
                $model = new Organization();
                break;
            default:
                throw new \Exception("No model for role $role");
        }

        return $model;
    }

    /**
     * @param ModelInterface $model
     *
     * @return BelongsToMany
     */
    public static function fieldsOfModel(ModelInterface $model)
    {
        $method = SecurityContext::role() . "Fields";

        return $model->$method();
    }


}