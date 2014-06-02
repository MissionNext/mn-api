<?php
namespace MissionNext\Controllers\Admin;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use MissionNext\Models\Application\Application;

class ApplicationController extends AdminBaseController {

    public function index()
    {
        $applications = Application::orderBy('id')->paginate(5);

        return View::make('admin.application.applications', array(
            'applications' => $applications,
        ));
    }

    public function create()
    {
        if ($this->request->isMethod('post')) {

            Input::flash();
            $rules = array(
                'app_name' => 'required|min:3',
                'public_key' => 'required|min:3',
                'private_key' => 'required|min:3'
            );

            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {

                return Redirect::route('applicationCreate')->withInput()->withErrors($validator);
            }

            $newApp = new Application();
            $newApp->name = Input::get('app_name');
            $newApp->public_key = Input::get('public_key');
            $newApp->private_key = Input::get('private_key');
            $newApp->save();

            return Redirect::route('applications');
        }

        return View::make('admin.application.create');
    }

    public function edit($id) {

        $application = Application::find($id);

        if(is_null($application)) {

            return Redirect::route('applications');
        }

        if ($this->request->isMethod('post')) {
            Input::flash();
            $rules = array(
                'name' => 'required|min:3',
                'public_key' => 'required|min:3',
            );
            $validator = Validator::make(Input::only('name', 'public_key'), $rules);
            if ($validator->fails()) {

                return Redirect::route('applicationEdit', array('id'=> $id))->withInput()->withErrors($validator);
            }

            $private_key = Input::get('private_key');

            $application->name = Input::get('name');
            $application->public_key = Input::get('public_key');
            ($private_key == '') ? : $application->private_key = $private_key;
            $application->save();

            return Redirect::route('applications');
        }

        return View::make('admin.application.edit', array('application' => $application));
    }

    public function delete($id) {
        if($this->request->isMethod('delete')) {

            $application = Application::find($id);
            $application->delete();

            return Redirect::route('applications');
        } else {

            return Redirect::route('applications');
        }
    }

}