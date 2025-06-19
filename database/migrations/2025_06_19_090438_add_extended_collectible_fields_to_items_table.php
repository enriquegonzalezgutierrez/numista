<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            // Postcard specific
            $table->string('publisher_postcard')->nullable()->comment('To avoid conflict with comic publisher');
            $table->string('origin_location')->nullable();

            // Photo specific
            $table->string('photographer')->nullable();
            $table->string('location')->nullable();
            $table->string('technique')->nullable();

            // Military specific
            $table->string('conflict')->nullable();

            // Sports specific
            $table->string('sport')->nullable();
            $table->string('team')->nullable();
            $table->string('player')->nullable();
            $table->string('event')->nullable();

            // Movie specific
            $table->string('movie_title')->nullable();
            $table->string('character')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn([
                'publisher_postcard', 'origin_location', 'photographer', 'location',
                'technique', 'conflict', 'sport', 'team', 'player', 'event',
                'movie_title', 'character'
            ]);
        });
    }
};
