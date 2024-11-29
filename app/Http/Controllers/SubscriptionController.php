<?php

namespace App\Http\Controllers;

use App\Models\CardDetail;
use App\Models\SubscriptionDetail;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\StripeClient;
use App\Helpers\SubscriptionHelper;

class SubscriptionController extends Controller
{
    //
    public function loadSubscription(){

        $plans = SubscriptionPlan::where('enabled', 1)->get();
        return view('subscription', compact('plans'));
    }

    public function getPlanDetail(Request $request){
        try {

            $planId = $request->id;
            $planData = SubscriptionPlan::where('id', $planId)->first();
           
            $isHasActivePlan = SubscriptionDetail::where(['user_id' => auth()->user()->id, 'status' => 'active'])->count();
            $msg = '';
            if($isHasActivePlan == 0 && $planData->trial_days != null &&  $planData->trial_days != ''){
                $msg = "You will get ". $planData->trial_days . " days trial, after that we will charge ".$planData->amount." for ".$planData->name." subscription plan.";
            }
            else {
                $msg = "We will charge ".$planData->amount." for ".$planData->name." subscription plan.";
            }
            return response()->json(['success' => true, 'msg' => $msg, 'data' => $planData]);
            //code...
        } catch (\Exception $e) {
            //throw $th;
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        }
    }

    public function createSubscription(Request $request){
        try {
            $subscriptionData = null;
            $planId = $request->planId;
            $user_id =  auth()->user()->id;
            $stripe_key = config('services.stripe.secret_key');
            Stripe::setApiKey($stripe_key);

            $stripe = new StripeClient($stripe_key);
            $stripeData = $request->data;
            // create custoomer in stripe
            $customer = $this->createCustomer($stripeData['id']);
            $customer_id = $customer['id'];

            // get subscription plan
            $subscriptionPlan = SubscriptionPlan::where('id', $planId)->first();
            
            // start and change subscription condition
            $subscriptionDetail = SubscriptionDetail::where(['user_id' => $user_id, 'status' => 'active', 'cancel' => 0])->orderBy('id', 'desc')->first();
            // check any subscription avaialbel for user
            $subscriptionCount = SubscriptionDetail::where('user_id', $user_id)->count();
            /*
                Subscription types
                0 -> Monthly, 1-> Yearly, 2-> Lifetime
            */
            // if user wants change monthly to yearly
           
            if($subscriptionDetail && $subscriptionDetail->plan_interval == 'month' && $subscriptionPlan->type == 1){
                SubscriptionHelper::cancel_current_subscription($user_id, $subscriptionDetail);
                $subscriptionData = SubscriptionHelper::start_yearly_subscription($customer_id, $user_id, $subscriptionPlan, $stripe);
            }else if($subscriptionDetail && $subscriptionDetail->plan_interval == 'month' && $subscriptionPlan->type == 2){
                SubscriptionHelper::cancel_current_subscription($user_id, $subscriptionDetail);
                $subscriptionData = SubscriptionHelper::start_lifetime_subscription($customer_id, $user_id, auth()->user()->name, $subscriptionPlan, $stripe);
            }else if($subscriptionDetail && $subscriptionDetail->plan_interval == 'year' && $subscriptionPlan->type == 0){
                SubscriptionHelper::cancel_current_subscription($user_id, $subscriptionDetail);
                $subscriptionData = SubscriptionHelper::start_monthly_subscription($customer_id, $user_id, $subscriptionPlan, $stripe);
            }else if($subscriptionDetail && $subscriptionDetail->plan_interval == 'year' && $subscriptionPlan->type == 2){
                SubscriptionHelper::cancel_current_subscription($user_id, $subscriptionDetail);
                $subscriptionData = SubscriptionHelper::start_lifetime_subscription($customer_id, $user_id, auth()->user()->name, $subscriptionPlan, $stripe);
            }else {
                // not avaialble any subscription 
                if($subscriptionCount == 0){
                    // give new user trial plan
                    if($subscriptionPlan->type == 0){ 
                        $subscriptionData = SubscriptionHelper::start_monthly_trial_subscription($customer_id, $user_id, $subscriptionPlan );
                    }else if($subscriptionPlan->type == 1){
                        $subscriptionData = SubscriptionHelper::start_yearly_trial_subscription($customer_id, $user_id, $subscriptionPlan );
                    }else if($subscriptionPlan->type == 2){
                        $subscriptionData = SubscriptionHelper::start_lifetime_trial_subscription($customer_id, $user_id, $subscriptionPlan );
                    }
                }else { // user all subscriptions are canlled

                    if($subscriptionPlan->type == 0){ // monthly subscription
                       SubscriptionHelper::capture_monthly_pending_fees($customer_id, $user_id, auth()->user()->name , $subscriptionPlan, $stripe);
                       $subscriptionData = SubscriptionHelper::start_monthly_subscription($customer_id, $user_id, $subscriptionPlan, $stripe);
                    }else if($subscriptionPlan->type == 1){ // yearly subscription 
                        SubscriptionHelper::capture_yearly_pending_fees($customer_id, $user_id, auth()->user()->name , $subscriptionPlan, $stripe);
                        $subscriptionData = SubscriptionHelper::start_yearly_subscription($customer_id, $user_id, $subscriptionPlan, $stripe);
                    }else if($subscriptionPlan->type == 2){ // lifetime subscription
                        $subscriptionData = SubscriptionHelper::start_lifetime_subscription($customer_id, $user_id, auth()->user()->name, $subscriptionPlan, $stripe);
                    }
                }
            }
            //
            
           
            $this->saveCardDetail($stripeData, $user_id, $customer_id);

            if($subscriptionData != null){
                return response()->json(['success' => true, 'msg' => 'Subscription Purchased!']);
            }else {
                return response()->json(['success' => false, 'msg' => 'Subscription Purchased Failed!']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'msg' => $e->getMessage()]);
        }
    }

    public function createCustomer($token_id){
        $customer = Customer::create([
            'name'=> auth()->user()->name,
            'email' => auth()->user()->email,
            'source' => $token_id
        ]);

        return $customer;
    }

    function saveCardDetail($cardData, $user_id, $customer_id){
       
        CardDetail::updateOrCreate(
            [
                'user_id' => $user_id,
                'card_no' => $cardData['card']['last4']
            ],
            [
                "customer_id" => $customer_id,
                "user_id" => $user_id,
                "card_id" => $cardData['card']['id'],
                "name" => $cardData['card']['name'],
                'card_no' => $cardData['card']['last4'],
                "brand" =>  $cardData['card']['brand'],
                "month" => $cardData['card']['exp_month'],
                "year" => $cardData['card']['exp_year'],
                "created_at" => date('Y-m-d H:i:s'),
                "updated_at" => date('Y-m-d H:i:s')
            ]
        );

       
    }
}
