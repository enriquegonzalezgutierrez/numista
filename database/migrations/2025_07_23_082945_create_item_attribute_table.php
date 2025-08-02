<?php

// database/migrations/2025_07_23_082945_create_item_attribute_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This is the pivot table connecting an Item to a SharedAttribute and storing its value.
     */
    public function up(): void
    {
        Schema::create('item_attribute', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shared_attribute_id')->constrained('shared_attributes')->cascadeOnDelete();

            // This stores the actual value for text, number, date attributes.
            $table->text('value')->nullable();

            // This links to a predefined option if the attribute is of type 'select'.
            $table->foreignId('attribute_option_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();

            // An item should only have one value for a given attribute.
            $table->unique(['item_id', 'shared_attribute_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_attribute');
    }
};
