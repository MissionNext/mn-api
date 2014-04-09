<?php
namespace Api\User;

use Api\BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use MissionNext\Api\Exceptions\UserException;
use MissionNext\Api\Exceptions\ValidationException;
use MissionNext\Api\Response\RestResponse;
use Illuminate\Support\Facades\Request;
use MissionNext\Filter\RouteSecurityFilter;
use MissionNext\Models\Role\Role;

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


        return new RestResponse($this->userRepo()->all());
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
     * @throws \MissionNext\Api\Exceptions\UserException
     * @throws \MissionNext\Api\Exceptions\ValidationException
     */
    public function store()
    {
        $validationData = [ "password" => Input::get("password"),
            "email" =>  Input::get("email"),
            "role" => Input::get("role"),
            "username" => Input::get("username"),
        ];

        $constraints = [
            "password" => "required|between:3,100",
            "email" => "required|unique:users,email|email",
            "role" => "exists:roles,role",
            "username" => "required|unique:users,username"
        ];
        /** @var  $validator \Illuminate\Validation\Validator */
        $validator = Validator::make(
            $validationData,
            $constraints
        );
        if ($validator->fails())
        {
            throw new ValidationException($validator->messages());
        }

        /** @var  $req \Symfony\Component\HttpFoundation\Request */
        $profileData = Input::except("timestamp","username","password","email","role");
        $roleName = Input::get('role');
        if (!RouteSecurityFilter::isAllowedRole($roleName)){

            throw new UserException("Role '{$roleName}' doesn't exists", UserException::ON_CREATE);
        }
        $userRep = $this->userRepo();
        $user = $userRep->getModel();
        $user->setPassword(Input::get('password'));
        $user->setEmail(Input::get('email'));
        $user->setUsername(Input::get('username'));
        $user->setRole(Role::whereRole( $roleName )->firstOrFail());
        $this->updateUserProfile($user, $profileData);

        return new RestResponse($user);
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

        return new RestResponse($this->userRepo()->find($id));
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
        $user = $this->userRepo()->find($id);
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
        $user = $this->userRepo()->find($id);
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

        return new RestResponse($this->userRepo()->getModel()->whereRaw($str, $arrV)->get());
    }

    /**
     * @return RestResponse
     */
    public function check()
    {
        $password = Request::input('password');
        $username = Request::input('username');
        $user = $this->userRepo()->getModel()->whereUsername($username)->first();
        $user && !Hash::check($password, $user->password) && $user = null;

        return new RestResponse($user);
    }

}