<?php

namespace MissionNext\Api\Service\Payment;


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

class AuthorizeNet extends AbstractPaymentGateway implements ISecurityContextAware
{
    private $discount;
    private $fee;
    private $apps;

    public  function __construct(\AuthorizeNetAIM $authorizeNet, Application $app)
    {
        $this->paymentGateWay = $authorizeNet;

        $this->discount = GlobalConfig::whereKey(GlobalConfig::SUBSCRIPTION_DISCOUNT)->firstOrFail()->value;
        $this->fee = GlobalConfig::whereKey(GlobalConfig::CON_FEE)->firstOrFail()->value;
        $this->apps = $this->prepareApps(AppModel::with('configs')->with('subConfigs')->get()->toArray());
    }

    public function processRequest($data){

        $user = User::find($data['user_id']);

        $price = $this->calcPrice($data['subscriptions'], $user->role(), $data['period'], $data['type'], $data['coupon']);

        if($price > 0){
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
                'authorize_id' => $subscription_id
            );
        }

        return $response;
    }

    private function calcPrice($sites, $role, $period, $type, $coupon = null){

        $price = 0;
        $site_number = 0;

        foreach($sites as $site){

            if(!isset($this->apps[$site['id']]) ||
               !isset($this->apps[$site['id']]['sub_configs'][$role][$site['partnership']]) ||
               !isset($this->apps[$site['id']]['sub_configs'][$role][$site['partnership']]['price_'.$period])
            ){
                throw new BadDataException("Subscription error");
            }

            $price += $this->apps[$site['id']]['sub_configs'][$role][$site['partnership']]['price_'.$period];
            $site_number++;
        }

        if($site_number > 1){
            $price -= ( $price * $this->discount ) / 100;
        }

        if($coupon){
            $price -= $coupon['value'];
        }

        if($type == 'echeck'){
            $price += $this->fee;
        }

        return round($price);
    }

} 