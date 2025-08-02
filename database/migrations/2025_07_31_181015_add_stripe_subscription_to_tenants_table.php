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
        Schema::table('tenants', function (Blueprint $table) {
            // Stripe Customer ID: Each tenant will be a "Customer" in Stripe.
            $table->string('stripe_customer_id')->nullable()->index()->after('slug');

            // Stripe Subscription ID: The ID of their active subscription.
            $table->string('stripe_subscription_id')->nullable()->unique()->after('stripe_customer_id');

            // Subscription Status: e.g., 'active', 'past_due', 'canceled'.
            $table->string('subscription_status')->nullable()->after('stripe_subscription_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'stripe_customer_id',
                'stripe_subscription_id',
                'subscription_status',
            ]);
        });
    }
};
