<?php
/**
 * Created by WyTcorp.
 * User: WyTcorp
 * Date: 28.09.22
 * Site: lockit.com.ua
 * Email: wild.savedo@gmail.com
 */

namespace App\Modules\Admin\Dashboard\Controllers;

use App\Models\Coupon\Coupon as Coupons;
use App\Modules\Admin\BaseController;
use App\Modules\Admin\Dashboard\Requests\CouponsRequestWeb;
use App\Modules\Admin\Dashboard\Services\CouponsService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class CouponsController extends BaseController
{
    /**
     * LanguageController constructor.
     */
    public function __construct(CouponsService $couponsService)
    {
        parent::__construct();
        $this->service = $couponsService;
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $this->title = 'Dashboard.Coupons list';
        $coupons = Coupons::query()->orderBy('id', 'DESC')->paginate(15);
        $this->content = view('Admin::Coupons.coupons')->with([
            'title' => $this->title,
            'coupons' => $coupons
        ])->render();
        return $this->renderOutput();
    }

    /**
     * @return Application|Factory|View
     */
    public function create(string $generate = null)
    {
        if (!empty($generate)) {
            $randomString = Str::random(30);
        }
        $this->title = 'Dashboard. Creating new Coupon';
        $this->content = view('Admin::Coupons.create')->with([
            'title' => $this->title,
            'randomString' => $randomString ?? null
        ])->render();
        return $this->renderOutput();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CouponsRequestWeb $request
     * @return Response
     */
    public function store(CouponsRequestWeb $request)
    {
        $model = $this->service->saveWeb($request, new Coupons());
        $name = $model->code;
        return \Redirect::route('dashboards.coupons.index')->with([
            'message' => "Success! Coupon {$name} successfully created"
        ]);
    }

    /**
     * @param Coupons $coupons
     * @return Application|Factory|View
     */
    public function edit(Coupons $coupon, string $generate = null)
    {
        if (!empty($generate)) {
            $randomString = Str::random(30);
        }
        $this->title = 'Dashboard. Coupons language';
        $this->content = view('Admin::Coupons.edit')->
        with([
            'title' => $this->title,
            'item' => $coupon,
            'randomString' => $randomString ?? null
        ])->
        render();

        return $this->renderOutput();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param CouponsRequestWeb $request
     * @param Coupons $coupon
     * @return Response
     */
    public function update(CouponsRequestWeb $request, Coupons $coupon)
    {
        $model = $this->service->saveWeb($request, $coupon);
        $name = $model->code;
        return \Redirect::route('dashboards.coupons.index')->with([
            'message' => "Success! Coupon {$name} successfully updated"
        ]);
    }

    /**
     * @param Coupons $coupon
     * @return Response
     */
    public function destroy(Coupons $coupon)
    {
        $name = $coupon->code;
        $coupon->delete();
        return \Redirect::route('dashboards.coupons.index')->with([
            'alert' => "Success! Coupon {$name} successfully deleted"
        ]);
    }

}
