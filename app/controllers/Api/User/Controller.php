<?php
namespace Api\User;

use Api\BaseController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use MissionNext\Api\Exceptions\ProfileException;
use MissionNext\Api\Exceptions\UserException;
use MissionNext\Api\Response\RestResponse;
use Illuminate\Support\Facades\Request;
use MissionNext\Filter\RouteSecurityFilter;
use MissionNext\Models\Field\FieldFactory;
use MissionNext\Models\User\User as UserModel;
use MissionNext\Models\Role\Role;
use MissionNext\Repos\User\UserRepository;

/**
 * Class Controller
 * @package Api\User
 * @description User Controller
 */
class Controller extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return RestResponse
     */
    public function index()
    {


        return new RestResponse($this->userRepository()->all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return RestResponse
     */
    public function create()
    {
        //
    }

    /**
     * @return RestResponse
     *
     * @throws \MissionNext\Api\Exceptions\UserException
     */
    public function store()
    {
        /** @var  $req \Symfony\Component\HttpFoundation\Request */
        $profileData = Input::except("timestamp","username","password","email","role");
        $roleName = Input::get('role');
        if (!RouteSecurityFilter::isAllowedRole($roleName)){

            throw new UserException("Role '{$roleName}' doesn't exists", UserException::ON_CREATE);
        }
        $userRep = $this->userRepository();
        $user = $userRep->getModel();
        $user->setPassword(Input::get('password'));
        $user->setEmail(Input::get('email'));
        $user->setUsername(Input::get('username'));
        $user->setRole(Role::whereRole( $roleName )->firstOrFail());
        $user->save();

        return new RestResponse($this->updateUserProfile($user, $profileData));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return RestResponse
     */
    public function show($id)
    {

        return new RestResponse(UserModel::find($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return RestResponse
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     *
     * @return RestResponse
     */
    public function update($id)
    {
        $user = UserModel::findOrFail($id);
        $data = Request::only(["username", "email", "password"]);
        $filteredData = array_filter($data);
        foreach ($filteredData as $prop => $val) {
            $user->$prop = $prop === "password" ? Hash::make($val) : $val;
        }
        $user->save();

        return new RestResponse($user);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return RestResponse
     */
    public function destroy($id)
    {
        $user = UserModel::findOrFail($id);
        $user->delete();

        return new RestResponse($user);
    }

    /**
     * @return RestResponse
     */
    public function find()
    {
        $searchByData = Request::only(["username", "email"]);
        $searchByData = array_filter($searchByData);
        $str = '';
        $arrV = [];
        for ($c = count($searchByData), $i = 0; $i < $c; $i++) {
            $isAnd = $i !== ($c - 1) ? ' and ' : '';
            $str .= key($searchByData) . " = ?" . $isAnd;
            $arrV[] = current($searchByData);
            next($searchByData);
        }
        $users = UserModel::whereRaw($str, $arrV)->get();

        return new RestResponse($users);
    }

    /**
     * @return RestResponse
     */
    public function check()
    {
        $password = Request::input('password');
        $username = Request::input('username');
        $user = UserModel::whereUsername($username)->first();
        $user && !Hash::check($password, $user->password) && $user = null;

        return new RestResponse($user);
    }

}