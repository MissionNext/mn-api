<?php
/**
 * Created by WyTcorp.
 * User: WyTcorp
 * Date: 26.09.22
 * Site: lockit.com.ua
 * Email: wild.savedo@gmail.com
 */

namespace App\Modules\Admin\Dashboard\Controllers;

use App\Models\Application\Application as Applications;
use App\Models\Subscription\SubConfig;
use App\Modules\Admin\BaseController;
use App\Modules\Admin\Dashboard\Requests\ApplicationRequestWeb;
use App\Modules\Admin\Dashboard\Services\ApplicationService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;


class ApplicationController extends BaseController
{
    /**
     * ApplicationController constructor.
     */
    public function __construct(ApplicationService $applicationService)
    {
        parent::__construct();
        $this->service = $applicationService;
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $this->title = 'Dashboard. Application list';
        $this->content = view('Admin::Application.applications')->with([
            'title' => $this->title,
            'username' => Auth::user()->username,
            'applications' => Applications::all()->sortBy(['id', 'ASC']),
        ])->render();
        return $this->renderOutput();
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        $this->title = 'Dashboard. Creating new application';
        $this->content = view('Admin::Application.create')->with([
            'title' => $this->title
        ])->render();
        return $this->renderOutput();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ApplicationRequestWeb $request
     * @return Response
     */
    public function store(ApplicationRequestWeb $request)
    {
        $model = $this->service->saveWeb($request, new Applications());
        $configs = SubConfig::defConfig();
        foreach ($configs as $config) {
            foreach ($config['partnership'] as $p) {
                SubConfig::updateOrCreate([
                    'app_id' => $model->id,
                    'partnership' => $p['level'],
                    'role' => $config['role']['key']
                ], [
                    "price_month" => $p['price_month'],
                    "price_year" => $p['price_year'],
                    "partnership_status" => $p["partnership_status"],
                ]);
            }
        }
        $name = $model->name;
        return \Redirect::route('dashboards.application.index')->with([
            'message' => "Success! Application {$name} successfully created"
        ]);
    }

    /**
     * @param Applications $application
     * @return Application|Factory|View
     */
    public function edit(Applications $application)
    {
        $this->title = 'Dashboard. Editing application';
        $this->content = view('Admin::Application.edit')->
        with([
            'title' => $this->title,
            'item' => $application,
        ])->
        render();

        return $this->renderOutput();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ApplicationRequestWeb $request
     * @param Applications $application
     * @return Response
     */
    public function update(ApplicationRequestWeb $request, Applications $application)
    {
        $model = $this->service->saveWeb($request, $application);
        $name = $model->name;
        return \Redirect::route('dashboards.application.index')->with([
            'message' => "Success! Application {$name} successfully updated"
        ]);
    }

    /**
     * @param Applications $application
     * @return Response
     */
    public function destroy(Applications $application)
    {
        $name = $application->name;
        $application->delete();
        return \Redirect::route('dashboards.application.index')->with([
            'alert' => "Success! Application {$name} successfully deleted"
        ]);
    }
}
