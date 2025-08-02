<?php

// app/Console/Commands/SetupMeilisearch.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Meilisearch\Client;
// ADD THIS LINE
use Numista\Collection\Domain\Models\Item;

class SetupMeilisearch extends Command
{
    protected $signature = 'scout:setup';

    protected $description = 'Configure Meilisearch filterable and sortable attributes for the application models.';

    public function handle(Client $meilisearch): int
    {
        $this->info('Configuring Meilisearch indexes...');

        try {
            // THE FINAL FIX: Instead of hardcoding the index name, we get it from the model.
            // The searchableAs() method on the Searchable trait automatically includes the SCOUT_PREFIX.
            $itemIndexName = (new Item)->searchableAs();

            $this->info("Configuring index: '{$itemIndexName}'");

            // Configure the dynamically named index
            $index = $meilisearch->index($itemIndexName);

            $index->updateFilterableAttributes([
                'status',
                'type',
                'tenant_name',
                'categories',
                'attributes',
            ]);

            $index->updateSortableAttributes([
                'created_at',
                'sale_price',
            ]);

            $this->info("✅ Meilisearch index '{$itemIndexName}' configured successfully!");

        } catch (\Exception $e) {
            $this->error('Failed to connect or configure Meilisearch: '.$e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
