<?php

// database/migrations/2025_07_23_082841_create_shared_attributes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This table defines the canonical list of attributes for the entire platform.
     */
    public function up(): void
    {
        Schema::create('shared_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // The attribute name must be unique across the platform.
            $table->string('type'); // e.g., text, number, date, select
            $table->boolean('is_filterable')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shared_attributes');
    }
};
