<?php
/**
 * Created by WyTcorp.
 * User: WyTcorp
 * Date: 28.09.22
 * Site: lockit.com.ua
 * Email: wild.savedo@gmail.com
 */

namespace App\Modules\Admin\Dashboard\Services;

use App\Models\Configs\GlobalConfig;
use App\Models\Subscription\SubConfig;
use App\Modules\Admin\Dashboard\Requests\SubscriptionsRequestWeb;
use Illuminate\Database\Eloquent\Model;

class SubscriptionsService
{
    public function saveWeb(SubscriptionsRequestWeb $request, Model $model) {

       $organization_price_month = $request->organization_price_month;
       $organization_price_year = $request->organization_price_year;
       $organization_partnership_status = $request->organization_partnership_status;
       $agency_price_month = $request->agency_price_month;
       $agency_price_year = $request->agency_price_year;
       $candidate_price_month = $request->candidate_price_month;
       $candidate_price_year = $request->candidate_price_year;
       $subscription_discount = $request->subscriptionDiscount;
       $con_fee = $request->conFee;
       $grace_period = $request->gracePeriod;

        $app_id = $model->id;
        $organizations = SubConfig::query()->where([['app_id', $app_id], ['role', 'organization']])->orderBy('id')->get();
        foreach ($organizations as $organization){
            if ($organization->partnership ==='limited'){
                $organization->price_month = $organization_price_month['limited'];
                $organization->price_year = $organization_price_year['limited'];
                $organization->partnership_status = isset($organization_partnership_status['limited'])?true:false;
                $organization->save();
            }
            if ($organization->partnership ==='basic'){
                $organization->price_month = $organization_price_month['basic'];
                $organization->price_year = $organization_price_year['basic'];
                $organization->save();
            }
            if ($organization->partnership ==='plus'){
                $organization->price_month = $organization_price_month['plus'];
                $organization->price_year = $organization_price_year['plus'];
                $organization->partnership_status = isset($organization_partnership_status['plus'])?true:false;
                $organization->save();
            }
        }
        $agency = SubConfig::query()->where([['app_id', $app_id], ['role', 'agency']])->orderBy('id')->get()->first();
        $agency->price_month = $agency_price_month;
        $agency->price_year = $agency_price_year;
        $agency->save();
        $candidate = SubConfig::query()->where([['app_id', $app_id], ['role', 'candidate']])->orderBy('id')->get()->first();
        $candidate->price_month = $candidate_price_month;
        $candidate->price_year = $candidate_price_year;
        $candidate->save();
        $globalConfig = GlobalConfig::all();
        foreach ($globalConfig as $globa){
            if ($globa->key ==='subscriptionDiscount'){
                $globa->value = $subscription_discount;
                $globa->save();
            }
            if ($globa->key ==='conFee'){
                $globa->value = $con_fee;
                $globa->save();
            }
            if ($globa->key ==='gracePeriod'){
                $globa->value = $grace_period;
                $globa->save();
            }
        }
        $model->fill($request->only($model->getFillable()));
        $model->save();
        return $model;
    }
}
