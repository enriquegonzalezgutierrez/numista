<?php

// app/Filament/ItemTypeManager.php

namespace Numista\Collection\UI\Filament;

use Numista\Collection\UI\Filament\ItemTypes\ArtType;
use Numista\Collection\UI\Filament\ItemTypes\BanknoteType;
use Numista\Collection\UI\Filament\ItemTypes\BookType;
use Numista\Collection\UI\Filament\ItemTypes\CameraType;
use Numista\Collection\UI\Filament\ItemTypes\CoinType;
use Numista\Collection\UI\Filament\ItemTypes\ComicType;
use Numista\Collection\UI\Filament\ItemTypes\JewelryType;
use Numista\Collection\UI\Filament\ItemTypes\MilitaryCollectibleType;
use Numista\Collection\UI\Filament\ItemTypes\MovieCollectibleType;
use Numista\Collection\UI\Filament\ItemTypes\PenType;
use Numista\Collection\UI\Filament\ItemTypes\PhotoType;
use Numista\Collection\UI\Filament\ItemTypes\PostcardType;
use Numista\Collection\UI\Filament\ItemTypes\RadioType;
use Numista\Collection\UI\Filament\ItemTypes\SportsCollectibleType;
use Numista\Collection\UI\Filament\ItemTypes\StampType;
use Numista\Collection\UI\Filament\ItemTypes\ToyType;
use Numista\Collection\UI\Filament\ItemTypes\VehicleType;
use Numista\Collection\UI\Filament\ItemTypes\VinylRecordType;
use Numista\Collection\UI\Filament\ItemTypes\WatchType;

class ItemTypeManager
{
    /**
     * A map of item type keys to their corresponding handler classes.
     * The key MUST match the value in the 'type' select dropdown.
     *
     * @var array<string, string>
     */
    protected array $types = [
        // --- Types WITH custom form fields ---
        'coin' => CoinType::class,
        'banknote' => BanknoteType::class,
        'comic' => ComicType::class,
        'watch' => WatchType::class,
        'book' => BookType::class,
        'stamp' => StampType::class,
        'vehicle' => VehicleType::class,
        'art' => ArtType::class,
        'jewelry' => JewelryType::class,
        'pen' => PenType::class,
        'camera' => CameraType::class,
        'vinyl_record' => VinylRecordType::class,
        'postcard' => PostcardType::class,
        'photo' => PhotoType::class,
        'toy' => ToyType::class,
        'military' => MilitaryCollectibleType::class,
        'sports' => SportsCollectibleType::class,
        'radio' => RadioType::class,
        'movie_collectible' => MovieCollectibleType::class,

        // --- Generic types WITHOUT custom form fields (value is null) ---
        'medal' => null,
        'antique' => null,
        'vintage_item' => null,
        'paper' => null,
        'craftsmanship' => null,
        'object' => null,
    ];

    /**
     * Get the specific form components for a given item type.
     */
    public function getFormComponentsForType(?string $typeKey): array
    {
        if (is_null($typeKey) || ! isset($this->types[$typeKey]) || is_null($this->types[$typeKey])) {
            return [];
        }

        $typeClass = $this->types[$typeKey];

        return $typeClass::getFormComponents();
    }

    /**
     * Dynamically generates the list of types for a Select field.
     * It reads the keys from the $types array, making it the single source of truth.
     *
     * @return array<string, string>
     */
    public function getTypesForSelect(): array
    {
        // Get all the keys from our single source of truth array
        $typeKeys = array_keys($this->types);

        $translatedTypes = [];
        foreach ($typeKeys as $type) {
            // The key is the database value (e.g., 'moneda')
            // The value is the translated string (e.g., 'Moneda') from lang/es/item.php
            $translatedTypes[$type] = __('item.type_'.$type);
        }

        // Sort the types alphabetically by their translated value
        asort($translatedTypes);

        return $translatedTypes;
    }
}
