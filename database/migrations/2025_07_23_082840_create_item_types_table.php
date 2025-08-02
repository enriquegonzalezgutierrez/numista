<?php

// database/migrations/2025_07_23_082840_create_item_types_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This table will store the canonical list of available item types.
     */
    public function up(): void
    {
        Schema::create('item_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g., 'coin', 'watch', 'stamp'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_types');
    }
};
