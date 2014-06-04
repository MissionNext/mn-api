<?php
namespace MissionNext\Controllers\Admin;

use Illuminate\Support\Facades\View;
use MissionNext\Models\Language\LanguageModel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class LanguageController extends AdminBaseController {

    /**
     *
     * @return \Illuminate\View\View
     */
    public function index() {
        $languages = LanguageModel::orderBy('id')->paginate(15);

        return View::make('admin.language.languages', array('langs' => $languages));
    }

    /**
     *
     * @return \Illuminate\View\View
     */
    public function create() {
        if ($this->request->isMethod('post')) {
            Input::flash();
            $rules = array(
                'key' => 'required',
                'name' => 'required|min:3',
            );

            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {

                return Redirect::route('languageCreate')->withInput()->withErrors($validator);
            }

            $language = new LanguageModel();
            $language->key = Input::get('key');
            $language->name = Input::get('name');
            $language->save();

            $name = $language->name;
            Session::flash('info', "language <strong>$name</strong> successfully created");

            return Redirect::route('languages');
        }

        return View::make('admin.language.create');
    }

    /**
     * @param $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id) {
        $language = LanguageModel::find($id);

        if(is_null($language)) {
            Session::flash('warning', "language with ID $id not found");

            return Redirect::route('languages');
        }
        if ($this->request->isMethod('post')) {
            Input::flash();
            $rules = array(
                'key' => 'required',
                'name' => 'required|min:3',
            );
            $validator = Validator::make(Input::all(), $rules);
            if ($validator->fails()) {

                return Redirect::route('languageEdit', array('id'=> $id))->withInput()->withErrors($validator);
            }

            $language->key = Input::get('key');
            $language->name = Input::get('name');
            $language->save();
            $name = $language->name;
            Session::flash('info', "language <strong>$name</strong> successfully updated");

            return Redirect::route('languages');
        }

        return View::make('admin.language.edit', array('language' => $language));
    }

    public function delete($id) {
        if($this->request->isMethod('delete')) {

            $language = LanguageModel::find($id);
            $name = $language->name;
            $language->delete();
            Session::flash('info', "language <strong>$name</strong> successfully deleted");

            return Redirect::route('languages');
        } else {

            return Redirect::route('languages');
        }
    }
}
