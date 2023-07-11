<?php

namespace App\Modules\Admin\Dashboard\Controllers;

use App\Modules\Admin\BaseController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class DashboardController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $this->title = 'Dashboard home page';
        $this->content  = view('Admin::Dashboard.index')->with([
            'title' => $this->title,
            'username' => Auth::user()->username
        ])->render();
        return $this->renderOutput();
    }

}
