<?php 

namespace App\Helpers;

use App\Models\SubscriptionDetail;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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
            echo $e->getMessage();
        }
       
    }

    
}
