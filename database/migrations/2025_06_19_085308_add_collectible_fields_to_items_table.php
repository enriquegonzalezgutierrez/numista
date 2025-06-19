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
            // Common fields for many types
            $table->string('brand')->nullable()->comment('For watches, pens, cameras, vehicles, etc.');
            $table->string('model')->nullable()->comment('For watches, pens, cameras, vehicles, etc.');
            $table->string('material')->nullable()->comment('For jewelry, antiques, pens, etc.');

            // Book specific
            $table->string('author')->nullable();
            $table->string('isbn')->nullable();

            // Art specific
            $table->string('artist')->nullable();
            $table->string('dimensions')->nullable();

            // Jewelry specific
            $table->string('gemstone')->nullable();

            // Vehicle specific
            $table->string('license_plate')->nullable();
            $table->string('chassis_number')->nullable();

            // Music / Vinyl specific
            $table->string('record_label')->nullable();

            // Stamp specific
            $table->string('face_value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('items', function (Blueprint $table) {
        $table->dropColumn([
            'brand', 'model', 'material', 'author', 'isbn', 'artist',
            'dimensions', 'gemstone', 'license_plate', 'chassis_number',
            'record_label', 'face_value'
        ]);
    });
}
};
