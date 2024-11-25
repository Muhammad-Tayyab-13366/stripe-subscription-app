<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionDetail;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;

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
                $msg = "You will get ". $planData->trial_days . "days trial, after that we will charge ".$planData->amount." for ".$planData->name." subscription plan.";
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
}
