<?php
// database/seeders/AttributeValueSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Numista\Collection\Domain\Models\Attribute;

class AttributeValueSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding predefined attribute values...');

        // Find the 'Grade' attribute
        $gradeAttribute = Attribute::where('name', 'Grade')->first();

        if ($gradeAttribute) {
            // Clear any previous values for this attribute
            $gradeAttribute->values()->delete();

            // The keys are what we will store in the database
            $gradeKeys = ['unc', 'au', 'xf', 'vf', 'f', 'g'];

            // Create the value records in the database using the keys
            foreach ($gradeKeys as $key) {
                $gradeAttribute->values()->create(['value' => $key]);
            }
        }
    }
}