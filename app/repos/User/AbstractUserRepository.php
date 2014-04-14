<?php


namespace MissionNext\Repos\User;


use MissionNext\Models\ProfileInterface;
use MissionNext\Repos\AbstractRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Collection;
use MissionNext\Models\Profile;
use MissionNext\Models\Field\FieldType;

abstract class AbstractUserRepository extends AbstractRepository
{
    /**
     * @param $id
     * @param array $columns
     * @return ProfileInterface
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function find($id, $columns = array('*'))
    {
        $this->model = parent::find($id, $columns);

        if (!$this->model) {

            throw new NotFoundHttpException(class_basename($this)." with id $id not found");
        }

        return $this->model;
    }

    /**
     * @param Collection $fields
     *
     * @return Profile
     */
    public function profileStructure(Collection $fields)
    {
        $profile = new Profile();

        $fields->each(function ($field) use ($profile) {
            $key = $field->symbol_key;
            if (isset($profile->$key)) {
                $profile->$key = array_merge($profile->$key, [$field->pivot->value]);
            } else {
                $profile->$key = FieldType::isMultiple($field->type) ? [$field->pivot->value] : $field->pivot->value;
            }
        });

        return $profile;

    }
} 