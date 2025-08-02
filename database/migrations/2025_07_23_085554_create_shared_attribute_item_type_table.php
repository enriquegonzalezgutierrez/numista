<?php

// database/migrations/2025_07_23_085554_create_shared_attribute_item_type_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This pivot table connects a global attribute to one or more item types.
     */
    public function up(): void
    {
        // THE FIX: Drop the old table if it exists to apply the new structure.
        Schema::dropIfExists('shared_attribute_item_type');

        Schema::create('shared_attribute_item_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shared_attribute_id')->constrained('shared_attributes')->cascadeOnDelete();

            // THE FIX: Use a proper foreign key to the new item_types table.
            $table->foreignId('item_type_id')->constrained('item_types')->cascadeOnDelete();

            $table->timestamps();

            // Each attribute should only be linked to an item type once.
            $table->unique(['shared_attribute_id', 'item_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_attribute_item_type');
    }
};
