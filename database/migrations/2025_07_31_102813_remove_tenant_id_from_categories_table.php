<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration globalizes the categories by removing the tenant_id.
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['tenant_id']);
            // Then drop the column
            $table->dropColumn('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     * This method adds the tenant_id column back for rollback purposes.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Add the column back, making it nullable to avoid issues with existing data.
            $table->foreignId('tenant_id')->nullable()->constrained()->cascadeOnDelete();
        });
    }
};
