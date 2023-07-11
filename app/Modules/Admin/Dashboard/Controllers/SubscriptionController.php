<?php
/**
 * Created by WyTcorp.
 * User: WyTcorp
 * Date: 28.09.22
 * Site: lockit.com.ua
 * Email: wild.savedo@gmail.com
 */

namespace App\Modules\Admin\Dashboard\Controllers;

use App\Models\Application\Application as Applications;
use App\Models\Configs\GlobalConfig;
use App\Models\Subscription\SubConfig;
use App\Modules\Admin\BaseController;
use App\Modules\Admin\Dashboard\Requests\SubscriptionsRequestWeb;
use App\Modules\Admin\Dashboard\Services\SubscriptionsService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;

class SubscriptionController extends BaseController
{
    /**
     * SubscriptionController constructor.
     */
    public function __construct(SubscriptionsService $subscriptionsService)
    {
        parent::__construct();
        $this->service = $subscriptionsService;
    }

    /**
     * @return Application|Factory|View
     */
    public function index(int $subscription)
    {
        $this->title = 'Dashboard.Subscription Management';
        $organizations = SubConfig::query()->where([['app_id', $subscription], ['role', 'organization']])->orderBy('id')->get();
        $agency = SubConfig::query()->where([['app_id', $subscription], ['role', 'agency']])->orderBy('id')->get()->first();
        $candidate = SubConfig::query()->where([['app_id', $subscription], ['role', 'candidate']])->orderBy('id')->get()->first();
        $application = Applications::find($subscription);
        $globalConfig = GlobalConfig::all();
        $this->content = view('Admin::Subscriptions.subscriptions')->with([
            'title' => $this->title,
            'application' => $application,
            'subscription' => $subscription,
            'organizations' => $organizations,
            'agency' => $agency,
            'candidate' => $candidate,
            'globalConfig' => $globalConfig,
        ])->render();
        return $this->renderOutput();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SubscriptionsRequestWeb $request
     * @param Applications $application
     * @return Response
     */
    public function update(SubscriptionsRequestWeb $request, Applications $application)
    {
        $model = $this->service->saveWeb($request, $application);
        return \Redirect::route('dashboards.subscriptions.index',['subscription'=>$model->id])->with([
            'message' => "Success! Subscriptions successfully updated"
        ]);
    }
}
