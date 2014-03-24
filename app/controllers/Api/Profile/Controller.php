<?php
namespace Api\Profile;

use Api\BaseController;
use MissionNext\Api\Response\RestResponse;
use MissionNext\Models\Field\FieldStrategy;
use MissionNext\Models\User\User as UserModel;
use MissionNext\Models\Profile;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request as Req;
use MissionNext\Models\Field\Candidate as CanFields;


class Controller extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		//
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
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return RestResponse
	 */
	public function show($id)
	{
        /** @var  $user UserModel */
		$user = UserModel::findOrFail($id);

        return new RestResponse($this->generateProfile($user));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{

	}

    /**
     * @param $id
     *
     * @return RestResponse
     *
     * @throws \Exception
     */
    public function update($id)
	{
        //@TODO uses only candidate profile
        /** @var  $user UserModel */
        $user = UserModel::findOrFail($id);
        /** @var  $request Req */
        $request = Request::instance();
        $hash = $request->request->all();
        if (empty($hash)){
            throw new \Exception("No hash values", 10);
        }
        $mapping = [];
        $fieldNames = array_keys($hash);
        $fieldModel = FieldStrategy::getModelName();
        $fieldModelMethod = FieldStrategy::getModelMethod();
        $fields = $fieldModel::whereIn('symbol_key', $fieldNames)->get();
        if ($fields->count() !== count($hash)){
            throw new \Exception("Wrong field name(s)", 11);
        }
        foreach($fields as $field){
           if (isset($hash[$field->symbol_key])){
               $mapping[$field->id] = ["value" => $hash[$field->symbol_key]];
           }
        }
        foreach($mapping as $key=>$map){
            $user->$fieldModelMethod()->detach($key, $map);
            $user->$fieldModelMethod()->attach($key, $map);
        }
        if (!empty($mapping)) {
            $user->touch();
        }
        // $user->fields()->sync($mapping); // delete fields that are not in mappings, update and insert what's new
       // dd( $queries = \DB::getQueryLog());

        return new RestResponse($this->generateProfile($user));
    }

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

    protected function generateProfile(UserModel $user){
        $profile = new Profile();
        $fieldModelMethod = FieldStrategy::getModelMethod();
        $user->$fieldModelMethod->each(function($field) use ($profile){
            $key = $field->symbol_key;
            $profile->$key = $field->pivot->value;
        });

        return $profile;
    }

}