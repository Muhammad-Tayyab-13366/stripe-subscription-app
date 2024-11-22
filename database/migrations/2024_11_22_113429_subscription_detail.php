<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subscription_details', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('stripe_subscription_id')->nullable();
            $table->string('stripe_subscription_schedule_id')->nullable();
            $table->string('stripe_customer_id');
            $table->string('subscription_plan_price_id');
            $table->float('plan_amount', 10,2);
            $table->string('plan_amount_currency');
            $table->string('plan_interval');
            $table->string('plan_interval_count');
            $table->timestamp('created');
            $table->timestamp('plan_period_start')->nullable();
            $table->timestamp('plan_period_end')->nullable();
            $table->timestamp('trial_end')->nullable();
            $table->enum('status', ['active', 'cancelled']);
            $table->integer('cancel')->default(0)->comment('0->active, 1->cancelled');
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_details');
    }
};
