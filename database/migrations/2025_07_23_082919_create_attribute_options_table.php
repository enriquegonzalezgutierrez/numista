<?php

// database/migrations/2025_07_23_082919_create_attribute_options_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This table holds the predefined selectable options for attributes of type 'select'.
     */
    public function up(): void
    {
        Schema::create('attribute_options', function (Blueprint $table) {
            $table->id();
            // Link to the shared_attributes table
            $table->foreignId('shared_attribute_id')->constrained('shared_attributes')->cascadeOnDelete();
            $table->string('value'); // e.g., "UNC", "AU", "Silver", "Gold"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attribute_options');
    }
};
