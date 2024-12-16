<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;


use App\Models\SubscriptionDetail;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\StripeClient;
use App\Helpers\SubscriptionHelper;
use Exception;
use Illuminate\Support\Facades\Log;

class UpdateTrialSubcription extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-trial-subcription';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Trial User subscriptio into real';

    protected $STRIPE_SECRET_KEY;
    public function __construct()
    {
        $this->STRIPE_SECRET_KEY = config('services.stripe.secret_key');

        parent::__construct();
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{
        //
            $secretKey = $this->STRIPE_SECRET_KEY;
            Stripe::setApiKey($secretKey);
            $stripe = new StripeClient( $secretKey);

            $SubscriptionDetail = SubscriptionDetail::with('user')->where(['status' => 'active', 'cancel' => 0])
            ->where('plan_period_end', '<', date('Y-m-d H:i:s'))
            ->whereNotNull('trial_end')
            ->orderBy('id', 'desc')
            ->get();
            \Log::info($SubscriptionDetail);
            if(count($SubscriptionDetail) > 0){
                foreach ($SubscriptionDetail as $key => $detail) {
                    $subscriptionPlan = SubscriptionPlan::where('stripe_price_id', $detail->subscription_plan_price_id)->first();
                    
                    if($detail->plan_interval == 'month'){
                        //, $detail->user_id,$detail->user->name,$subscriptionPlan,$stripe);
                        SubscriptionHelper::capture_monthly_pending_fees($detail->stripe_customer_id, $detail->user_id, $detail->user->name , $subscriptionPlan, $stripe);
                        SubscriptionHelper::renew_monthly_subscription($detail, $detail->user_id, $subscriptionPlan, $stripe);
                    }else if($detail->plan_interval == 'year'){
                        SubscriptionHelper::capture_yearly_pending_fees($detail->stripe_customer_id, $detail->user_id, $detail->user->name , $subscriptionPlan, $stripe);
                        SubscriptionHelper::renew_yearly_subscription($detail, $detail->user_id, $subscriptionPlan, $stripe);
                    }else if($detail->plan_interval == 'lifetime'){
                        SubscriptionHelper::renew_lifetime_subscription($detail->stripe_customer_id, $detail->user_id, $detail->user->name , $subscriptionPlan, $stripe);
                    }
                }
            }
        }catch(Exception $e){
            \Log::info('ooo');
        }
    }
}
