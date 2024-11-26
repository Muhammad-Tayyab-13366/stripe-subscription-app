<?php

namespace App\Http\Controllers;

use App\Models\CardDetail;
use App\Models\SubscriptionDetail;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Customer;


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
            $planId = $request->planId;
            $user_id =  auth()->user()->id;
            $stripe_key = config('services.stripe.secret_key');
            $stripe = Stripe::setApiKey($stripe_key);
            $stripeData = $request->data;
            // create custoomer in stripe
            $customer = $this->createCustomer($stripeData['id']);
            $customer_id = $customer['id'];

            // get subscription plan
            $subscriptionPlan = SubscriptionPlan::where('id', $planId)->first();
            /*
                Subscription types
                0 -> Monthly, 1-> Yearly, 2-> Lifetime
            */
            if($subscriptionPlan->type == 0){

            }else if($subscriptionPlan->type == 1){

            }else if($subscriptionPlan->type == 2){

            }

            if($customer){
                $this->saveCardDetail($stripeData, $user_id, $customer_id);
                return response()->json(['success' => true, 'msg' => $customer ]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => true, 'msg' => $e->getMessage()]);
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
        DB::enableQueryLog();
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

        $lastQuery = DB::getQueryLog();

        // Get the most recent query
        $lastQuery = end($lastQuery); // Retrieves the last query from the query log

        // Output the last query and bindings
        dd($lastQuery);
    }
}
