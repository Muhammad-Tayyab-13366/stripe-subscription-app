<?php 

namespace App\Helpers;

use App\Models\SubscriptionDetail;
use App\Models\User;
use App\Models\PendingFee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Subscription;
use Stripe\StripeClient;
class SubscriptionHelper{

    public static function start_monthly_trial_subscription($customer_id, $user_id, $subscriptionPlan ){
        
        try {
            //DB::enableQueryLog();
           
            $stripeData = null;
            $current_period_start = date('Y-m-d H:i:s');
            $date = date('Y-m-d 23:59:59');
            $trailDays = $subscriptionPlan->trial_days;
            $trailDaysEnd = date('Y-m-d H:i:s', strtotime($date.'+'.$trailDays.'Days'));

            $subscriptionDetail = [
                "user_id" => $user_id,
                "stripe_subscription_id" => NULL,
                "stripe_subscription_schedule_id" => '',
                "stripe_customer_id" => $customer_id,
                "subscription_plan_price_id" => $subscriptionPlan->stripe_price_id,
                "plan_amount" => $subscriptionPlan->amount,
                "plan_amount_currency" => 'usd',
                "plan_interval"  => 'month',
                "plan_interval_count" => 1,
                "created" => date('Y-m-d H:i:s'),
                "plan_period_start" => $current_period_start,
                "plan_period_end" =>  $trailDaysEnd,
                "trial_end" =>  $trailDaysEnd,
                "status" => 'active'
            ];

            $stripeData = SubscriptionDetail::updateOrCreate(
                [
                    'user_id' => $user_id,
                    'stripe_customer_id' => $customer_id,
                    "subscription_plan_price_id" => $subscriptionPlan->stripe_price_id
                ],
                $subscriptionDetail
            );
           

            User::where('id', $user_id)->update(['is_subscribed' => 1]);

            // $lastQuery = DB::getQueryLog();

            // // Get the most recent query
            // $lastQuery = end($lastQuery); // Retrieves the last query from the query log
    
            // // Output the last query and bindings
            // print_r($lastQuery);
            return  $stripeData ;

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
       
    }


    public static function start_yearly_trial_subscription($customer_id, $user_id, $subscriptionPlan ){
        
        try {
            //DB::enableQueryLog();
           
            $stripeData = null;
            $current_period_start = date('Y-m-d H:i:s');
            $date = date('Y-m-d 23:59:59');
            $trailDays = $subscriptionPlan->trial_days;
            $trailDaysEnd = date('Y-m-d H:i:s', strtotime($date.'+'.$trailDays.'Days'));

            $subscriptionDetail = [
                "user_id" => $user_id,
                "stripe_subscription_id" => NULL,
                "stripe_subscription_schedule_id" => '',
                "stripe_customer_id" => $customer_id,
                "subscription_plan_price_id" => $subscriptionPlan->stripe_price_id,
                "plan_amount" => $subscriptionPlan->amount,
                "plan_amount_currency" => 'usd',
                "plan_interval"  => 'year',
                "plan_interval_count" => 1,
                "created" => date('Y-m-d H:i:s'),
                "plan_period_start" => $current_period_start,
                "plan_period_end" =>  $trailDaysEnd,
                "trial_end" =>  $trailDaysEnd,
                "status" => 'active'
            ];

            $stripeData = SubscriptionDetail::updateOrCreate(
                [
                    'user_id' => $user_id,
                    'stripe_customer_id' => $customer_id,
                    "subscription_plan_price_id" => $subscriptionPlan->stripe_price_id
                ],
                $subscriptionDetail
            );
           

            User::where('id', $user_id)->update(['is_subscribed' => 1]);

            // $lastQuery = DB::getQueryLog();

            // // Get the most recent query
            // $lastQuery = end($lastQuery); // Retrieves the last query from the query log
    
            // // Output the last query and bindings
            // print_r($lastQuery);
            return  $stripeData ;

        } catch (\Exception $e) {
            echo $e->getMessage();
        }
       
    }

    public static function start_lifetime_trial_subscription($customer_id, $user_id, $subscriptionPlan ){
        
        try {
            //DB::enableQueryLog();
           
            $stripeData = null;
            $current_period_start = date('Y-m-d H:i:s');
            $date = date('Y-m-d 23:59:59');
            $trailDays = $subscriptionPlan->trial_days;
            $trailDaysEnd = date('Y-m-d H:i:s', strtotime($date.'+'.$trailDays.'Days'));

            $subscriptionDetail = [
                "user_id" => $user_id,
                "stripe_subscription_id" => NULL,
                "stripe_subscription_schedule_id" => '',
                "stripe_customer_id" => $customer_id,
                "subscription_plan_price_id" => $subscriptionPlan->stripe_price_id,
                "plan_amount" => $subscriptionPlan->amount,
                "plan_amount_currency" => 'usd',
                "plan_interval"  => 'lifetime',
                "plan_interval_count" => 1,
                "created" => date('Y-m-d H:i:s'),
                "plan_period_start" => $current_period_start,
                "plan_period_end" =>  $trailDaysEnd,
                "trial_end" =>  $trailDaysEnd,
                "status" => 'active'
            ];

            $stripeData = SubscriptionDetail::updateOrCreate(
                [
                    'user_id' => $user_id,
                    'stripe_customer_id' => $customer_id,
                    "subscription_plan_price_id" => $subscriptionPlan->stripe_price_id
                ],
                $subscriptionDetail
            );
           

            User::where('id', $user_id)->update(['is_subscribed' => 1]);

            // $lastQuery = DB::getQueryLog();

            // // Get the most recent query
            // $lastQuery = end($lastQuery); // Retrieves the last query from the query log
    
            // // Output the last query and bindings
            // print_r($lastQuery);
            return  $stripeData ;

        } catch (\Exception $e) {
            return null;
            //echo $e->getMessage();
        }
       
    }

    public static function capture_monthly_pending_fees($customer_id, $user_id, $user_name , $subscriptionPlan, $stripe){
        $totalAmount = $subscriptionPlan->amount;
        $daysInMonth = date('t');
        $currentDay = date('j');

       // \Log::info('days in month'. $daysInMonth);
       //  \Log::info('current days'. $currentDay);
        $amountForRestDays = ($daysInMonth - $currentDay) * ($totalAmount/$daysInMonth);
        $amountForRestDays = ceil($amountForRestDays);

        $stripeChargeData = $stripe->charges->create([
            'amount' => $amountForRestDays*100,
            'currency' => 'usd',
            'customer' => $customer_id,
            'description' => 'Monthly pending fees',
            'shipping' => [
                'name' => $user_name,
                'address' => [
                    'line1' => '123 Main sta',
                    'line2' => 'Apt 1',
                    'city' => 'Anytown',
                    'state' => 'NY',
                    'postal_code' => '12345',
                    'country' => 'US'

                ]
            ]
        ]);
        if(!empty($stripeChargeData)){
            $stripeCharge = $stripeChargeData->jsonSerialize();
            $chargeId = $stripeCharge['id'];
            $cusId = $stripeCharge['customer'];

            $pendingFeeData = [
                'user_id' => $user_id,
                'charge_id' => $chargeId,
                'customer_id' => $cusId,
                'amount' => $amountForRestDays,
                'created_at' => now()
            ];

            PendingFee::insert($pendingFeeData);

        }
        // \Log::info('Charge detail: '. $stripeCharge );
    }


    public static function start_monthly_subscription($customer_id, $user_id, $subscriptionPlan, $stripe){
        
        try {
            //DB::enableQueryLog();
           
            $stripeData = null;
            $currentMonthFirstDay = date('Y-m-01');
            $currentPeriodStart = date('Y-m-01 00:00:00', strtotime($currentMonthFirstDay." +1 Month"));
            $currentPeriodEnd = date('Y-m-t 23:59:59', strtotime($currentMonthFirstDay." +1 Month"));
            
            $stripeData = $stripe->subscriptions->create([
                'customer' => $customer_id,
                'items' => [
                    ['price' => $subscriptionPlan->stripe_price_id]
                ],
                'billing_cycle_anchor' =>strtotime($currentPeriodStart),
                'proration_behavior' => 'none'
            ]);

            if(!empty($stripeData)){
                $stripeData = $stripeData->jsonSerialize();
                $subscriptionId = $stripeData['id'];
                $customer_id = $stripeData['customer'];

                if(!empty($stripeData['items'])){
                    $planId = $stripeData['items']['data'][0]['plan']['id'];
                }else {
                    $planId = $stripeData['plan']['id'];
                }

                $plandData = $stripe->plans->retrieve(
                    $planId,
                    []
                );

                $planAmount = ($plandData->amount/100);
                $planCurrency = $plandData->currency;
                $planInterval = $plandData->interval;
                $planIntervalCount = $plandData->interval_count;
                $created = date('Y-m-d H:i:s', $stripeData['created']);
               // \Log::info('plandData: '. $plandData );

               $subscriptionDetail = [
                    "user_id" => $user_id,
                    "stripe_subscription_id" => $subscriptionId ,
                    "stripe_subscription_schedule_id" => '',
                    "stripe_customer_id" => $customer_id,
                    "subscription_plan_price_id" => $planId,
                    "plan_amount" => $planAmount ,
                    "plan_amount_currency" => $planCurrency,
                    "plan_interval"  => $planInterval,
                    "plan_interval_count" => $planIntervalCount,
                    "created" => $created,
                    "plan_period_start" => $currentPeriodStart,
                    "plan_period_end" =>  $currentPeriodEnd,
                    "status" => 'active',
                    "created_at" =>  now(),
                    "updated_at" => now()
                ];

                $stripeData = SubscriptionDetail::insert(
                    $subscriptionDetail
                );
                User::where('id', $user_id)->update(['is_subscribed' => 1]);

            }
            // print_r($stripeData);
            // \Log::info('stripeData: '. $stripeData );
           
            return  $stripeData ;

        } catch (\Exception $e) {
            //return null;
            echo $e->getMessage();
        }
       
    }


    public static function capture_yearly_pending_fees($customer_id, $user_id, $user_name , $subscriptionPlan, $stripe){
        
        $totalAmount = $subscriptionPlan->amount;
        $monthInYear = 12;
        $currentMonth = date('m')-1;

       // \Log::info('days in month'. $daysInMonth);
       //  \Log::info('current days'. $currentDay);
        $amountForRestMonths = ($monthInYear - $currentMonth) * ($totalAmount/$monthInYear);
        $amountForRestMonths = ceil($amountForRestMonths);

        $stripeChargeData = $stripe->charges->create([
            'amount' => $amountForRestMonths*100,
            'currency' => 'usd',
            'customer' => $customer_id,
            'description' => 'Yearly pending fees',
            'shipping' => [
                'name' => $user_name,
                'address' => [
                    'line1' => '123 Main sta',
                    'line2' => 'Apt 1',
                    'city' => 'Anytown',
                    'state' => 'NY',
                    'postal_code' => '12345',
                    'country' => 'US'

                ]
            ]
        ]);
        if(!empty($stripeChargeData)){
            $stripeCharge = $stripeChargeData->jsonSerialize();
            $chargeId = $stripeCharge['id'];
            $cusId = $stripeCharge['customer'];

            $pendingFeeData = [
                'user_id' => $user_id,
                'charge_id' => $chargeId,
                'customer_id' => $cusId,
                'amount' => $amountForRestMonths,
                'created_at' => now()
            ];

            PendingFee::insert($pendingFeeData);

        }
        // \Log::info('Charge detail: '. $stripeCharge );
    }


    public static function start_yearly_subscription($customer_id, $user_id, $subscriptionPlan, $stripe){
        
        try {
            //DB::enableQueryLog();
           
            $stripeData = null;
            $currentYear = date('Y');
            $currentPeriodStart = date('Y', strtotime("+1 Year")).'-01-01 00:00:00';
            $currentPeriodEnd = date('Y-12-t 23:59:59', strtotime($currentPeriodStart));
            
            $stripeData = $stripe->subscriptions->create([
                'customer' => $customer_id,
                'items' => [
                    ['price' => $subscriptionPlan->stripe_price_id]
                ],
                'billing_cycle_anchor' =>strtotime($currentPeriodStart),
                'proration_behavior' => 'none'
            ]);

            if(!empty($stripeData)){
                $stripeData = $stripeData->jsonSerialize();
                $subscriptionId = $stripeData['id'];
                $customer_id = $stripeData['customer'];

                if(!empty($stripeData['items'])){
                    $planId = $stripeData['items']['data'][0]['plan']['id'];
                }else {
                    $planId = $stripeData['plan']['id'];
                }

                $plandData = $stripe->plans->retrieve(
                    $planId,
                    []
                );

                $planAmount = ($plandData->amount/100);
                $planCurrency = $plandData->currency;
                $planInterval = $plandData->interval;
                $planIntervalCount = $plandData->interval_count;
                $created = date('Y-m-d H:i:s', $stripeData['created']);
               // \Log::info('plandData: '. $plandData );

               $subscriptionDetail = [
                    "user_id" => $user_id,
                    "stripe_subscription_id" => $subscriptionId ,
                    "stripe_subscription_schedule_id" => '',
                    "stripe_customer_id" => $customer_id,
                    "subscription_plan_price_id" => $planId,
                    "plan_amount" => $planAmount ,
                    "plan_amount_currency" => $planCurrency,
                    "plan_interval"  => $planInterval,
                    "plan_interval_count" => $planIntervalCount,
                    "created" => $created,
                    "plan_period_start" => $currentPeriodStart,
                    "plan_period_end" =>  $currentPeriodEnd,
                    "status" => 'active',
                    "created_at" =>  now(),
                    "updated_at" => now()
                ];

                $stripeData = SubscriptionDetail::insert(
                    $subscriptionDetail
                );
                User::where('id', $user_id)->update(['is_subscribed' => 1]);

            }
            // print_r($stripeData);
            // \Log::info('stripeData: '. $stripeData );
           
            return  $stripeData ;

        } catch (\Exception $e) {
            //return null;
            echo $e->getMessage();
        }
       
    }


    public static function start_lifetime_subscription($customer_id, $user_id, $user_name, $subscriptionPlan, $stripe){
        
        try {
            //DB::enableQueryLog();
           
            $stripeData = null;
            $currentYear = date('Y');
            $currentPeriodStart = date('Y-m-d H:i:s');
            $currentPeriodEnd = date('Y-m-d H:i:s', strtotime('2099-12-31 23:59:59'));
            
           

            $stripeChargeData = $stripe->charges->create([
                'amount' => $subscriptionPlan->amount*100,
                'currency' => 'usd',
                'customer' => $customer_id,
                'description' => 'One time paymnt for lifetime',
                'shipping' => [
                    'name' => $user_name,
                    'address' => [
                        'line1' => '123 Main sta',
                        'line2' => 'Apt 1',
                        'city' => 'Anytown',
                        'state' => 'NY',
                        'postal_code' => '12345',
                        'country' => 'US'

                    ]
                ]
            ]);
            if(!empty($stripeChargeData)){
                $stripeCharge = $stripeChargeData->jsonSerialize();
                $chargeId = $stripeCharge['id'];
                $cusId = $stripeCharge['customer'];

                $subscriptionDetail = [
                    "user_id" => $user_id,
                    "stripe_subscription_id" => $chargeId ,
                    "stripe_subscription_schedule_id" => NULL,
                    "stripe_customer_id" => $customer_id,
                    "subscription_plan_price_id" => $subscriptionPlan->stripe_price_id,
                    "plan_amount" => $subscriptionPlan->amount,
                    "plan_amount_currency" => 'usd',
                    "plan_interval"  => 'lifetime',
                    "plan_interval_count" => 1,
                    "created" => date('Y-m-d H:i:s'),
                    "plan_period_start" => $currentPeriodStart,
                    "plan_period_end" =>  $currentPeriodEnd,
                    "status" => 'active',
                    "created_at" =>  now(),
                    "updated_at" => now()
                ];

                $stripeData = SubscriptionDetail::insert(
                    $subscriptionDetail
                );
                User::where('id', $user_id)->update(['is_subscribed' => 1]);
    
            
            }
            
            // print_r($stripeData);
            // \Log::info('stripeData: '. $stripeData );
           
            return  $stripeData ;

        } catch (\Exception $e) {
            //return null;
            echo $e->getMessage();
        }
       
    }
    

    public static function cancel_current_subscription($user_id, $subscriptionDetail){
        try {
            $return = null;
            $stripe_key = config('services.stripe.secret_key');
            Stripe::setApiKey($stripe_key);
            $stripe = new StripeClient($stripe_key);

            if($subscriptionDetail->stripe_subscription_id != null && $subscriptionDetail->stripe_subscription_id !=''){
                $current_subscription = Subscription::retrieve($subscriptionDetail->stripe_subscription_id);
                $current_subscription->cancel();
            }

            $return = SubscriptionDetail::where('id', $subscriptionDetail->id)->update([
                "status" => "cancelled",
                "cancel" => 1,
                "cancelled_at" => date('Y-m-d H:i:s')
            ]);
           
            User::where('id', $user_id)->update(['is_subscribed' => 0]);

            return $return;
        } catch (\Exception $e) {
            return null;
           // echo $e->getMessage();
        }
    }

    public static function get_current_subscription(){
        $subscriptionDetail = SubscriptionDetail::where(['user_id' => auth()->user()->id,
            'status' => 'active',
            'cancel' => 0
        ])->orderBy('id', 'desc')->first();

        return $subscriptionDetail ;
        //$stripe_subscription_id = $subscriptionDetail->stripe_subscription_id;
        //$stripeSubscription = Subscription::retrieve($stripe_subscription_id);

    }


    public static function renew_monthly_subscription($subscriptionDetail, $user_id, $subscriptionPlan, $stripe){
        
        try {
            //DB::enableQueryLog();
           
            $stripeData = null;
            $currentMonthFirstDay = date('Y-m-01');
            $currentPeriodStart = date('Y-m-01 00:00:00', strtotime($currentMonthFirstDay." +1 Month"));
            $currentPeriodEnd = date('Y-m-t 23:59:59', strtotime($currentMonthFirstDay." +1 Month"));
            
            $stripeData = $stripe->subscriptions->create([
                'customer' => $subscriptionDetail->stripe_customer_id,
                'items' => [
                    ['price' => $subscriptionPlan->stripe_price_id]
                ],
                'billing_cycle_anchor' =>strtotime($currentPeriodStart),
                'proration_behavior' => 'none'
            ]);

            if(!empty($stripeData)){
                $stripeData = $stripeData->jsonSerialize();
                $subscriptionId = $stripeData['id'];
                $customer_id = $stripeData['customer'];

                if(!empty($stripeData['items'])){
                    $planId = $stripeData['items']['data'][0]['plan']['id'];
                }else {
                    $planId = $stripeData['plan']['id'];
                }

                $plandData = $stripe->plans->retrieve(
                    $planId,
                    []
                );

                $planAmount = ($plandData->amount/100);
                $planCurrency = $plandData->currency;
                $planInterval = $plandData->interval;
                $planIntervalCount = $plandData->interval_count;
                $created = date('Y-m-d H:i:s', $stripeData['created']);
               // \Log::info('plandData: '. $plandData );

               $subscriptionDetailData = [
                    "user_id" => $user_id,
                    "stripe_subscription_id" => $subscriptionId ,
                    "stripe_subscription_schedule_id" => '',
                    "stripe_customer_id" => $customer_id,
                    "subscription_plan_price_id" => $planId,
                    "plan_amount" => $planAmount ,
                    "plan_amount_currency" => $planCurrency,
                    "plan_interval"  => $planInterval,
                    "plan_interval_count" => $planIntervalCount,
                    "created" => $created,
                    "plan_period_start" => $currentPeriodStart,
                    "plan_period_end" =>  $currentPeriodEnd,
                    'trial_end' => NULL,
                    "status" => 'active',
                    "created_at" =>  now(),
                    "updated_at" => now()
                ];

                $stripeData = SubscriptionDetail::where('id', $subscriptionDetail->id)->update(
                    $subscriptionDetailData
                );
                User::where('id', $user_id)->update(['is_subscribed' => 1]);

            }
            // print_r($stripeData);
            // \Log::info('stripeData: '. $stripeData );
           
            return  $stripeData ;

        } catch (\Exception $e) {
            //return null;
            echo $e->getMessage();
        }
       
    }


    public static function renew_yearly_subscription($subscriptionDetail, $user_id, $subscriptionPlan, $stripe){
        
        try {
            //DB::enableQueryLog();
           
            $stripeData = null;
            $currentYear = date('Y');
            $currentPeriodStart = date('Y', strtotime("+1 Year")).'-01-01 00:00:00';
            $currentPeriodEnd = date('Y-12-t 23:59:59', strtotime($currentPeriodStart));
            
            $stripeData = $stripe->subscriptions->create([
                'customer' => $subscriptionDetail->stripe_customer_id,
                'items' => [
                    ['price' => $subscriptionPlan->stripe_price_id]
                ],
                'billing_cycle_anchor' =>strtotime($currentPeriodStart),
                'proration_behavior' => 'none'
            ]);

            if(!empty($stripeData)){
                $stripeData = $stripeData->jsonSerialize();
                $subscriptionId = $stripeData['id'];
                $customer_id = $stripeData['customer'];

                if(!empty($stripeData['items'])){
                    $planId = $stripeData['items']['data'][0]['plan']['id'];
                }else {
                    $planId = $stripeData['plan']['id'];
                }

                $plandData = $stripe->plans->retrieve(
                    $planId,
                    []
                );

                $planAmount = ($plandData->amount/100);
                $planCurrency = $plandData->currency;
                $planInterval = $plandData->interval;
                $planIntervalCount = $plandData->interval_count;
                $created = date('Y-m-d H:i:s', $stripeData['created']);
               // \Log::info('plandData: '. $plandData );

               $subscriptionDetailData = [
                    "user_id" => $user_id,
                    "stripe_subscription_id" => $subscriptionId ,
                    "stripe_subscription_schedule_id" => '',
                    "stripe_customer_id" => $subscriptionDetail->stripe_customer_id,
                    "subscription_plan_price_id" => $planId,
                    "plan_amount" => $planAmount ,
                    "plan_amount_currency" => $planCurrency,
                    "plan_interval"  => $planInterval,
                    "plan_interval_count" => $planIntervalCount,
                    "created" => $created,
                    "plan_period_start" => $currentPeriodStart,
                    "plan_period_end" =>  $currentPeriodEnd,
                    "trial_end" =>  NULL,
                    "status" => 'active',
                    "created_at" =>  now(),
                    "updated_at" => now()
                ];

                $stripeData = SubscriptionDetail::where('id', $subscriptionDetail->id)->update(
                    $subscriptionDetailData
                );
                User::where('id', $user_id)->update(['is_subscribed' => 1]);

            }
            // print_r($stripeData);
            // \Log::info('stripeData: '. $stripeData );
           
            return  $stripeData ;

        } catch (\Exception $e) {
            //return null;
            echo $e->getMessage();
        }
       
    }

    public static function renew_lifetime_subscription($subscriptionDetail, $user_id, $user_name, $subscriptionPlan, $stripe){
        
        try {
            //DB::enableQueryLog();
           
            $stripeData = null;
            $currentYear = date('Y');
            $currentPeriodStart = date('Y-m-d H:i:s');
            $currentPeriodEnd = date('Y-m-d H:i:s', strtotime('2099-12-31 23:59:59'));
            
           

            $stripeChargeData = $stripe->charges->create([
                'amount' => $subscriptionPlan->amount*100,
                'currency' => 'usd',
                'customer' => $subscriptionDetail->stripe_customer_id,
                'description' => 'One time paymnt for lifetime',
                'shipping' => [
                    'name' => $user_name,
                    'address' => [
                        'line1' => '123 Main sta',
                        'line2' => 'Apt 1',
                        'city' => 'Anytown',
                        'state' => 'NY',
                        'postal_code' => '12345',
                        'country' => 'US'

                    ]
                ]
            ]);
            if(!empty($stripeChargeData)){
                $stripeCharge = $stripeChargeData->jsonSerialize();
                $chargeId = $stripeCharge['id'];
                $cusId = $stripeCharge['customer'];

                $subscriptionDetailData = [
                    "user_id" => $user_id,
                    "stripe_subscription_id" => $chargeId ,
                    "stripe_subscription_schedule_id" => NULL,
                    "stripe_customer_id" => $subscriptionDetail->stripe_customer_id,
                    "subscription_plan_price_id" => $subscriptionPlan->stripe_price_id,
                    "plan_amount" => $subscriptionPlan->amount,
                    "plan_amount_currency" => 'usd',
                    "plan_interval"  => 'lifetime',
                    "plan_interval_count" => 1,
                    "created" => date('Y-m-d H:i:s'),
                    "plan_period_start" => $currentPeriodStart,
                    "plan_period_end" =>  $currentPeriodEnd,
                    "trial_end" => NULL,
                    "status" => 'active',
                    "created_at" =>  now(),
                    "updated_at" => now()
                ];

                $stripeData = SubscriptionDetail::where('id', $subscriptionDetail->id)->insert(
                    $subscriptionDetailData
                );
                User::where('id', $user_id)->update(['is_subscribed' => 1]);
    
            
            }
            
            // print_r($stripeData);
            // \Log::info('stripeData: '. $stripeData );
           
            return  $stripeData ;

        } catch (\Exception $e) {
            //return null;
            echo $e->getMessage();
        }
       
    }
}
