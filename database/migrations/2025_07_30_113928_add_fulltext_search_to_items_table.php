<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only apply this PostgreSQL-specific index when the connection is 'pgsql'.
        // This prevents errors when running tests with SQLite.
        if (DB::getDriverName() === 'pgsql') {
            Schema::table('items', function (Blueprint $table) {
                // Add a tsvector index that will store the searchable text.
                // It's generated automatically from 'name' and 'description' in Spanish.
                $table->rawIndex(
                    "to_tsvector('spanish', name || ' ' || description)",
                    'items_fulltext_search_index',
                    'gin'
                );
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Also make the down method conditional to avoid errors on rollback in non-pgsql environments.
        if (DB::getDriverName() === 'pgsql') {
            Schema::table('items', function (Blueprint $table) {
                $table->dropIndex('items_fulltext_search_index');
            });
        }
    }
};
