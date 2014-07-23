<?php

namespace MissionNext\Api\Service\Payment;


use Carbon\Carbon;
use Illuminate\Foundation\Application;
use MissionNext\Api\Auth\ISecurityContextAware;
use MissionNext\Api\Exceptions\AuthorizeException;
use MissionNext\Api\Exceptions\BadDataException;
use MissionNext\Models\Configs\GlobalConfig;
use MissionNext\Models\Application\Application as AppModel;
use MissionNext\Models\Coupon\Coupon;
use MissionNext\Models\DataModel\BaseDataModel;
use MissionNext\Models\Subscription\Partnership;
use MissionNext\Models\User\User;
use MissionNext\Repos\Subscription\SubscriptionRepository;
use MissionNext\Repos\Subscription\SubscriptionRepositoryInterface;

class AuthorizeNet extends AbstractPaymentGateway implements ISecurityContextAware
{
    private $discount;
    private $fee;
    private $apps;
    private $defaults;

    private $part_multiplier;
    private $renew_type;

    private $app;

    public  function __construct(\AuthorizeNetAIM $authorizeNet, \AuthorizeNetARB $authorizeNetARB, Application $app)
    {
        $this->recurringBilling = $authorizeNetARB;
        $this->paymentGateWay = $authorizeNet;
        $this->app = $app;

        $this->discount = GlobalConfig::whereKey(GlobalConfig::SUBSCRIPTION_DISCOUNT)->firstOrFail()->value;
        $this->fee = GlobalConfig::whereKey(GlobalConfig::CON_FEE)->firstOrFail()->value;
        $this->apps = $this->prepareApps(AppModel::with('configs')->with('subConfigs')->get()->toArray());
    }

    public function processRequest($data){

        $this->defaults = $this->getDefaults($data['user_id']);

        $user = User::find($data['user_id']);

        $price = $this->calcPrice($data['subscriptions'], $user->role(), $data['period'], $data['type'], $data['coupon'], $data['renew_type']);

        $this->renew_type = $data['renew_type'];

        if($price > 0){
            if($data['renew_type'] == 'm'){

                $this->cancelAuthorizeSubs();

                return $this->processARB($user, $data, $price);
            } else {
                return $this->processAIM($user, $data, $price);
            }

        } else {

            if($data['coupon']){
                Coupon::disable($data['coupon']['code']);
            }

            $transaction_id = 0;

            $response = $this->prepareSubscriptionResponse($transaction_id, $price, $user['id'], $user->role(), $data['period'], $data['recurring'], $data['subscriptions']);

            return $response;
        }

    }

    /**
     * @return \AuthorizeNetAIM
     */
    public function getService()
    {
        return $this->paymentGateWay;
    }

    private function processAIM($user, $data, $price){
        $this->paymentGateWay->amount   = $price;

        if($data['type'] == 'cc'){
            $this->paymentGateWay->card_num = $data['payment_data']['card_num'];
            $this->paymentGateWay->exp_date = $data['payment_data']['exp_date'];
        } elseif( $data['type'] == 'echeck'){
            $this->paymentGateWay->setECheck(
                $data['payment_data']['aba_number'],
                $data['payment_data']['acct_number'],
                $data['payment_data']['acct_type'],
                $data['payment_data']['bank_name'],
                $data['payment_data']['bank_name'],
                'WEB'
            );
        } else {
            throw new BadDataException("Wrong payment type");
        }

        $this->paymentGateWay->invoice_num = time();

        $description = $this->addAppsToPayment($data['subscriptions'], $user->role(), $data['period']);

        $this->paymentGateWay->description = $description;

        $this->paymentGateWay->setFields($data['required_data']);

        $this->paymentGateWay->setCustomFields($data['additional_data']);

        $response = $this->paymentGateWay->authorizeAndCapture();

        if($response->approved){

            if($data['coupon']){
                Coupon::disable($data['coupon']['code']);
            }

            $transaction_id = $response->transaction_id;

            $response = $this->prepareSubscriptionResponse($transaction_id, $price, $user['id'], $user->role(), $data['period'], $data['recurring'], $data['subscriptions']);

            return $response;
        } else {
            throw new AuthorizeException($response->response_reason_text);
        }
    }

    private function processARB($user, $data, $price){

        $this->paymentGateWay->amount   = $price;

        $sub = new \AuthorizeNet_Subscription();

        $sub->amount = "100.00";

        $sub->bankAccountNameOnAccount = $data['payment_data']['bank_name'];
        $sub->bankAccountRoutingNumber = $data['payment_data']['aba_number'];
        $sub->bankAccountAccountNumber = $data['payment_data']['acct_number'];
        $sub->bankAccountAccountType = strtolower($data['payment_data']['acct_type']);
        $sub->bankAccountBankName = $data['payment_data']['bank_name'];
        $sub->bankAccountEcheckType = "WEB";

        $sub->startDate = Carbon::now()->day >= SubscriptionRepository::RECURRENT_DAY
            ? Carbon::now()->addMonth()->day(SubscriptionRepository::RECURRENT_DAY)->toDateString()
            : Carbon::now()->day(SubscriptionRepository::RECURRENT_DAY)->toDateString();
        $sub->intervalLength = "1";
        $sub->intervalUnit = "months";
        $sub->totalOccurrences = "9999";

        $sub->billToFirstName = $data['required_data']['first_name'];
        $sub->billToLastName = $data['required_data']['last_name'];
        $sub->billToAddress = $data['required_data']['address'];
        $sub->billToZip = $data['required_data']['zip'];
        $sub->billToState = $data['required_data']['state'];
        $sub->billToCountry = $data['required_data']['country'];
        $sub->billToCity = $data['required_data']['city'];

        $sub->customerId = $user['id'];

        if($data['coupon']){
            $sub->trialOccurrences = 1;
            $sub->trialAmount = ($price >= $data['coupon']['value'])?$price >= $data['coupon']['value']:0;
        }

        $response = $this->recurringBilling->createSubscription($sub);

        if($sub_id = $response->getSubscriptionId()){

            if($data['coupon']){
                Coupon::disable($data['coupon']['code']);
            }

            $response = $this->prepareSubscriptionResponse('', $price, $user['id'], $user->role(), $data['period'], $data['recurring'], $data['subscriptions'], '', $sub_id);

            return $response;
        } else {
            throw new AuthorizeException($response->getErrorMessage());
        }
    }

    /**
     * @return \AuthorizeNetARB
     */
    public function getRecurringBilling()
    {

        return $this->recurringBilling;
    }

    private function addAppsToPayment($sites, $role, $period){

        $description = '';

        foreach($sites as $site){

            $app = $this->apps[$site['id']];

            $price = $this->apps[$site['id']]['sub_configs'][$role][$site['partnership']]['price_'.$period];

            $this->paymentGateWay->addLineItem($site['id'], $app['name'], $app['name'] . " for " . $period, 1, $price, 'NO');

            $description .= $this->buildSiteDescription($app['name'], $role, $site['partnership'], $period, $price ) . ", ";
        }

        return $description;
    }

    private function buildSiteDescription($name, $role, $partnership, $period, $price){

        if($partnership == Partnership::LIMITED && $period == Partnership::PERIOD_YEAR){
            $period = '90 days';
        }

        return sprintf("%s: %s%s - %s/$%s", $name, $role, $partnership?'/' . $partnership:'', $period, $price);
    }

    private function prepareApps($apps){

        $prepared = array();

        foreach($apps as $app){

            $papp = $app;

            $papp['configs'] = array();
            $papp['sub_configs'] = array(
                BaseDataModel::CANDIDATE => array(),
                BaseDataModel::AGENCY => array(),
                BaseDataModel::ORGANIZATION => array()
            );

            foreach($app['configs'] as $config){
                $papp['configs'][$config['key']] = $config['value'];
            }

            foreach($app['sub_configs'] as $sub_config){

                $papp['sub_configs'][$sub_config['role']][$sub_config['partnership']] = $sub_config;

            }

            $prepared[$papp['id']] = $papp;
        }

        return $prepared;
    }

    private function prepareSubscriptionResponse($transaction_id, $amount, $user_id, $role, $period, $recurrent, $sites, $comment = '', $subscription_id = 0){

        $response = array(
            'transaction_id' => $transaction_id,
            'comment' => $comment,
            'amount' => $amount,
            'subscriptions' => array()
        );

        foreach($sites as $site){

            $response['subscriptions'][] = array(
                'app_id' => $site['id'],
                'user_id' => $user_id,
                'price' => $this->apps[$site['id']]['sub_configs'][$role][$site['partnership']]['price_'.$period],
                'partnership' => $site['partnership'],
                'is_recurrent' => $recurrent,
                'period' => $period,
                'authorize_id' => $subscription_id,
                'type' => $this->renew_type
            );
        }

        return $response;
    }

    private function calcPrice($sites, $role, $period, $type, $coupon = null, $renew_type){

        $data = array();
        $partnerships = array();

        foreach($sites as $site){
            $partnerships[$site['id']] = $site['partnership'];
            $data[$site['id']] = $this->apps[$site['id']];
        }

        $new_sites = array_diff_key($data, $this->defaults);
        $removed = array_diff_key($this->defaults, $data);
        $keeped = array_intersect_key($this->defaults, $data);

        $total = 0;
        $site_number = 0;

        foreach($keeped as $id => $site){

            $site_number++;

            switch($renew_type){
                case 'k' : {
                    break;
                }
                case 't' : {
                    $total += $this->apps[$id]['sub_configs'][$role][$partnerships[$id]]['price_year'] - $site['left_amount'];
                    break;
                }
                case 'e' : {
                    $total += $this->apps[$id]['sub_configs'][$role][$partnerships[$id]]['price_year'];
                    break;
                }
                case 'm' : {
                    $total += $this->apps[$id]['sub_configs'][$role][$partnerships[$id]]['price_month'];
                    break;
                }
            }

        }

        foreach($new_sites as $id => $site){

            $part_price = $site['sub_configs'][$role][$partnerships[$id]]['price_year'] * $this->part_multiplier;
            $site_number++;

            switch($renew_type){
                case 'k' : {
                    $total += $part_price;
                    break;
                }
                case 't' : {
                    $total += $this->apps[$id]['sub_configs'][$role][$partnerships[$id]]['price_year'];
                    break;
                }
                case 'e' : {
                    $total += $this->apps[$id]['sub_configs'][$role][$partnerships[$id]]['price_year'] + $part_price;
                    break;
                }
                case 'm' : {
                    $total += $this->apps[$id]['sub_configs'][$role][$partnerships[$id]]['price_month'];
                    break;
                }
            }

        }

        foreach($removed as $id => $site){

            switch($renew_type){
                case 'k' : {
                    $total -= $site['left_amount'];
                    break;
                }
                case 't' : {
                    $total -= $site['left_amount'];
                    break;
                }
                case 'e' : {
                    $total -= $site['left_amount'];
                    break;
                }
                case 'm' : {
                    break;
                }
            }

        }

        if($site_number > 1){
            $total -= ( $total * $this->discount ) / 100;
        }

        if($coupon && $renew_type != 'm'){
            $total -= $coupon['value'];
        }

        if($type == 'echeck'){
            $total += $this->fee;
        }

        return round($total);
    }

    private function getDefaults($user_id){
        $_defaults = $this->app->make(SubscriptionRepositoryInterface::class)->userSubscriptions($user_id)->toArray();

        $defaults = array();

        $days_left = 0;
        $total_days = 0;

        foreach($_defaults as $default){

            $default['left_amount'] = round($default['paid'] * (  $default['days_left'] / ( (strtotime($default['end_date']) - strtotime($default['start_date'])) / (24*60*60) ) ));
            $defaults[$default['app_id']] = $default;

            if($default['partnership'] != Partnership::LIMITED && $default['days_left'] > $days_left){
                $days_left = $default['days_left'];
                $total_days = round( (strtotime($default['end_date']) - strtotime($default['start_date'])) / (24*60*60));
            }
        }

        $this->part_multiplier = $total_days > 0 ? $days_left / $total_days : 0;

        return $defaults;
    }

    private function cancelAuthorizeSubs(){

        $ids = array();

        foreach($this->defaults as $default){
            if($default['authorize_id']){
                $ids[] = $default['authorize_id'];
            }
        }

        $ids = array_unique($ids);

        foreach($ids as $id){
            $this->recurringBilling->cancelSubscription($id);
        }
    }

} 