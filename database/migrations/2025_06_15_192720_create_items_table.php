<?php

// database/migrations/YYYY_MM_DD_HHMMSS_create_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            // Core fields
            $table->id();
            $table->foreignId('tenant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();

            // Item Type Discriminator
            $table->string('type'); // 'coin', 'banknote', 'comic', etc.

            // Acquisition info
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->date('purchase_date')->nullable();

            // Sale info
            $table->string('status')->default('in_collection'); // 'in_collection', 'for_sale', 'sold'
            $table->decimal('sale_price', 10, 2)->nullable();

            // --- Type-specific Fields (all nullable) ---

            // Common for Coins/Banknotes
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('year')->nullable();
            $table->string('denomination')->nullable();
            $table->string('grade')->nullable(); // Can be used by all types

            // Coin specific
            $table->string('mint_mark')->nullable();
            $table->string('composition')->nullable();
            $table->decimal('weight', 8, 4)->nullable();

            // Banknote specific
            $table->string('serial_number')->nullable();

            // Comic specific
            $table->string('publisher')->nullable();
            $table->string('series_title')->nullable();
            $table->string('issue_number')->nullable();
            $table->date('cover_date')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
