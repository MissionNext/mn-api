<?php
namespace Api\User;

use Api\BaseController;
use Illuminate\Support\Facades\Hash;
use MissionNext\Api\Response\RestResponse;
use Illuminate\Support\Facades\Request;
use MissionNext\Models\User\User as UserModel;
use MissionNext\Models\Role\Role;
use MissionNext\Api\Auth\Token;

/**
 * Class Controller
 * @package Api\User
 * @description User Controller
 */
class Controller extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return RestResponse
	 */
	public function index()
	{
        return new RestResponse(UserModel::all());
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return RestResponse
	 */
	public function store()
	{
        $user = new UserModel;
        $user->password = Hash::make(Request::get('password'));
        $user->email = Request::get('email');
        $user->username = Request::get('username');
        $user->save();
        $user->roles()->attach(Role::ROLE_CANDIDATE);

        return new RestResponse($user);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{

		return new RestResponse(UserModel::find($id));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        $user = UserModel::findOrFail($id);
        $data = Request::only(["username","email","password"]);
        $filteredData = array_filter($data);
        foreach($filteredData as $prop=>$val){
            $user->$prop = $prop === "password" ? Hash::make($val) : $val;
        }
        $user->save();

        return new RestResponse($user);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
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
        $searchByData = Request::only(["username","email"]);
        $searchByData = array_filter($searchByData);
        $str = '';
        $arrV = [];
        for($c=count($searchByData), $i=0; $i < $c; $i++){
            $isAnd = $i !== ($c - 1) ? ' and ' : '';
            $str .= key($searchByData)." = ?".$isAnd;
            $arrV[] = current($searchByData);
            next($searchByData);
        }
        $users = UserModel::whereRaw($str,$arrV)->get();

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
        $user && !Hash::check($password, $user->password) && $user=null;

        return new RestResponse($user);
    }

}