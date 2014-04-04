<?php
namespace MissionNext\Repos\User;

use Illuminate\Support\Collection;
use MissionNext\Models\Field\FieldType;
use MissionNext\Models\Profile;
use MissionNext\Repos\AbstractRepository;
use MissionNext\Models\User\User;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserRepository extends AbstractRepository implements UserRepositoryInterface
{
    protected $modelClassName = User::class;

    /**
     * @return User
     */
    public function getModel()
    {

        return $this->model;
    }

    /**
     * @param $id
     * @param array $columns
     * @return User
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function find($id, $columns = array('*'))
    {
        $this->model = parent::find($id, $columns);

        if (!$this->model) {

            throw new NotFoundHttpException("User with id $id not found");
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