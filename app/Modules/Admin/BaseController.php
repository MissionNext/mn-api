<?php
/**
 * Created by WyTcorp.
 * User: WyTcorp
 * Date: 23.09.22
 * Site: lockit.com.ua
 * Email: wild.savedo@gmail.com
 */

namespace App\Modules\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application\Application as Applications;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{

    protected $template;
    protected $user;
    protected $title;
    protected $content;
    protected $sidebar;
    protected $vars;
    protected $locale;
    protected $service;

    public function __construct()
    {
        $this->template = "Admin::Dashboard.dashboard";
        $this->sidebar = $this->getMenu();
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            $this->locale = App::getLocale();
            return $next($request);
        });
    }

    protected function renderOutput()
    {
        $this->vars = Arr::add($this->vars, 'content', $this->content);
        $this->vars = Arr::add($this->vars, 'title', $this->title);

        $menu = $this->getMenu();

        $this->vars = Arr::add($this->vars, 'menu', $menu);

        return view($this->template)->with($this->vars);
    }

    private function getMenu()
    {
        $items = Applications::all()->sortBy('id');
        $arr = [];
        foreach ($items as $item){
            $arr[$item->id] = $item->name;
        }
        return $arr;
    }
}
