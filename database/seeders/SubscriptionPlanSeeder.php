<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $currentDateTime = Carbon::now()->format('Y-m-d H:i:s');
        SubscriptionPlan::insert([
            [
                "name" => 'Monthly',
                'stripe_price_id' => 'price_1QNvTYKgMiMJ028aUbRx3ypG',
                'trial_days' => 5,
                'amount' => 12,  // usd
                'type' => 0, // 0 - for Monthly
                'enabled' => 1,
                'created_at' => $currentDateTime,
                'updated_at' => $currentDateTime
            ],
            [
                "name" => 'Yearly',
                'stripe_price_id' => 'price_1QNvUTKgMiMJ028aY5OvrERa',
                'trial_days' => 5,
                'amount' => 100,  // usd
                'type' => 1, // 1 - for Yearly
                'enabled' => 1,
                'created_at' => $currentDateTime,
                'updated_at' => $currentDateTime
            ],
            [
                "name" => 'Lifetime',
                'stripe_price_id' => 'price_1QNvVPKgMiMJ028a1s5nE0n4',
                'trial_days' => 5,
                'amount' => 400, // usd
                'type' => 2, // 0 - for Lifetime
                'enabled' => 1,
                'created_at' => $currentDateTime,
                'updated_at' => $currentDateTime
            ]
            ]);
         

    }
}
