<?php

// database/migrations/YYYY_MM_DD_HHMMSS_create_countries_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('iso_code', 2)->unique(); // e.g., ES, US, MX
            // No timestamps needed for this reference table
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
