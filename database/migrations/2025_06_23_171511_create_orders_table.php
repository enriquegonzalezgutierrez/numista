<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->comment('The customer who placed the order')->constrained('users')->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->decimal('total_amount', 10, 2);
            $table->string('status')->default('pending'); // pending, paid, shipped, completed, cancelled
            $table->text('shipping_address')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->default('pending'); // pending, successful, failed
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
