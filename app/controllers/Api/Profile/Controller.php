<?php
namespace Api\Profile;

use Api\BaseController;
use MissionNext\Api\Exceptions\ProfileException;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Models\User\User as UserModel;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request as Req;


class Controller extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return RestResponse
     */
    public function index()
    {
        //
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
     * Store a newly created resource in storage.
     *
     * @return RestResponse
     */
    public function store()
    {
        //
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
        $profileFieldsQuery = $this->fieldRepo()->profileFields($this->userRepo()->find($id));

        return new RestResponse($this->userRepo()->profileStructure($profileFieldsQuery->get()));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return RestResponse
     */
    public function edit($id)
    {

    }

    /**
     * @param $id
     * @return RestResponse
     * @throws \MissionNext\Api\Exceptions\ProfileException
     */
    public function update($id)
    {
        /** @var  $user UserModel */
        $user = $this->userRepo()->find($id);
        /** @var  $request Req */
        $request = Request::instance();
        $hash = $request->request->all();
        if (empty($hash)) {

            throw new ProfileException("No values specified", ProfileException::ON_UPDATE);
        }
        $this->updateUserProfile($user, $hash);

        return new RestResponse( $this->userRepo()->profileStructure($this->fieldRepo()->profileFields($user)->get()));
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
        //
    }

}