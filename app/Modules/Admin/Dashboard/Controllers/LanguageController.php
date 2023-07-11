<?php
/**
 * Created by WyTcorp.
 * User: WyTcorp
 * Date: 27.09.22
 * Site: lockit.com.ua
 * Email: wild.savedo@gmail.com
 */

namespace App\Modules\Admin\Dashboard\Controllers;

use App\Models\Language\LanguageModel as Languages;
use App\Modules\Admin\BaseController;
use App\Modules\Admin\Dashboard\Requests\LanguagesRequestWeb;
use App\Modules\Admin\Dashboard\Services\LanguagesService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Language as LanguageHelper;

class LanguageController extends BaseController
{
    /**
     * LanguageController constructor.
     */
    public function __construct(LanguagesService $languagesService)
    {
        parent::__construct();
        $this->service = $languagesService;
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $this->title = 'Dashboard.Languages list';
        $this->content = view('Admin::Languages.languages')->with([
            'title' => $this->title,
            'username' => Auth::user()->username,
            'languages' => Languages::all()->sortBy(['id', 'ASC']),
        ])->render();
        return $this->renderOutput();
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        $this->title = 'Dashboard. Creating new language';
        $this->content = view('Admin::Languages.create')->with([
            'title' => $this->title,
            'languages' => LanguageHelper::$codes,
        ])->render();
        return $this->renderOutput();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param LanguagesRequestWeb $request
     * @return Response
     */
    public function store(LanguagesRequestWeb $request)
    {
        $model = $this->service->saveWeb($request, new Languages());
        $name = $model->name;
        return \Redirect::route('dashboards.languages.index')->with([
            'message' => "Success! Languages {$name} successfully created"
        ]);
    }

    /**
     * @param Languages $language
     * @return Application|Factory|View
     */
    public function edit(Languages $language)
    {
        $this->title = 'Dashboard. Editing language';
        $this->content = view('Admin::Languages.edit')->
        with([
            'title' => $this->title,
            'item' => $language,
            'languages' => LanguageHelper::$codes,
        ])->
        render();

        return $this->renderOutput();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param LanguagesRequestWeb $request
     * @param Languages $language
     * @return Response
     */
    public function update(LanguagesRequestWeb $request, Languages $language)
    {
        $model = $this->service->saveWeb($request, $language);
        $name = $model->name;
        return \Redirect::route('dashboards.languages.index')->with([
            'message' => "Success! Language {$name} successfully updated"
        ]);
    }

    /**
     * @param Languages $language
     * @return Response
     */
    public function destroy(Languages $language)
    {
        $name = $language->name;
        $language->delete();
        return \Redirect::route('dashboards.languages.index')->with([
            'alert' => "Success! Language {$name} successfully deleted"
        ]);
    }

}
