<?php

// app/Console/Commands/SetupMeilisearch.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Meilisearch\Client;

class SetupMeilisearch extends Command
{
    protected $signature = 'scout:setup';

    protected $description = 'Configure Meilisearch filterable and sortable attributes for the application models.';

    public function handle(Client $meilisearch): int
    {
        $this->info('Configuring Meilisearch indexes...');

        try {
            // Configure the 'items' index
            $meilisearch->index('items')->updateFilterableAttributes([
                'status',
                'type',
                'tenant_name',
                'categories',
                'attributes',
            ]);

            // Add any other indexes here in the future

            $this->info('✅ Meilisearch indexes configured successfully!');

        } catch (\Exception $e) {
            $this->error('Failed to connect or configure Meilisearch: '.$e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
