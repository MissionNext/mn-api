<?php
/**
 * Created by WyTcorp.
 * User: WyTcorp
 * Date: 28.09.22
 * Site: lockit.com.ua
 * Email: wild.savedo@gmail.com
 */

namespace App\Modules\Admin\Dashboard\Controllers;

use App\Models\Admin\AdminUserModel as Adminusers;
use App\Modules\Admin\BaseController;
use App\Modules\Admin\Dashboard\Requests\AdministratorRequestWeb;
use App\Modules\Admin\Dashboard\Services\AdministratorService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

class AdministratorController  extends BaseController
{
    /**
     * AdministratorController constructor.
     */
    public function __construct(AdministratorService $administratorService)
    {
        parent::__construct();
        $this->service = $administratorService;
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $this->title = 'Dashboard.Administrators list';
        $this->content = view('Admin::Administrators.administrators')->with([
            'title' => $this->title,
            'administrators' => Adminusers::all()->sortBy(['id', 'ASC']),
        ])->render();
        return $this->renderOutput();
    }

    /**
     * @return Application|Factory|View
     */
    public function create()
    {
        $this->title = 'Dashboard. Creating new administrator';
        $this->content = view('Admin::Administrators.create')->with([
            'title' => $this->title
        ])->render();
        return $this->renderOutput();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AdministratorRequestWeb $request
     * @return Response
     */
    public function store(AdministratorRequestWeb $request)
    {
        $model = $this->service->saveWeb($request, new Adminusers());
        $name = $model->username;
        return \Redirect::route('dashboards.administrators.index')->with([
            'message' => "Success! Administrator {$name} successfully created"
        ]);
    }

    /**
     * @param Adminusers $adminusers
     * @return Application|Factory|View
     */
    public function edit(Adminusers $administrator)
    {
        $this->title = 'Dashboard. Editing administrator';
        $this->content = view('Admin::Administrators.edit')->
        with([
            'title' => $this->title,
            'item' => $administrator
        ])->
        render();

        return $this->renderOutput();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param AdministratorRequestWeb $request
     * @param Adminusers $adminusers
     * @return Response
     */
    public function update(AdministratorRequestWeb $request, Adminusers $administrator)
    {
        $model = $this->service->saveWeb($request, $administrator);
        $name = $model->username;
        return \Redirect::route('dashboards.administrators.index')->with([
            'message' => "Success! Administrator {$name} successfully updated"
        ]);
    }

    /**
     * @param Adminusers $adminusers
     * @return Response
     */
    public function destroy(Adminusers $administrator)
    {
        $name = $administrator->username;
        $administrator->delete();
        return \Redirect::route('dashboards.administrators.index')->with([
            'alert' => "Success! Administrator {$name} successfully deleted"
        ]);
    }

}
