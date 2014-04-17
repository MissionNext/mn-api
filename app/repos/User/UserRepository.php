<?php
namespace MissionNext\Repos\User;


use MissionNext\Models\User\User;

class UserRepository extends AbstractUserRepository implements UserRepositoryInterface
{
    protected $modelClassName = User::class;

    /**
     * @return User
     */
    public function getModel()
    {

        return $this->model;
    }

    public function profileData($id)
    {

        $model = $this->getModel()->find($id);

        $role = $model->roles()->first()->role;

        return $model
            ->select( "users.username", $role."_profile.value", $role."_fields.symbol_key")
            ->leftJoin($role."_profile", "users.id", '=', $role."_profile.user_id" )
            ->leftJoin($role."_fields", $role."_profile.field_id", '=', $role.'_fields.id')
            ->where("users.id" ,"=", $id);

    }


} 