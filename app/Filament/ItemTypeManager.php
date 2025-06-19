<?php
// app/Filament/ItemTypeManager.php

namespace App\Filament;

use App\Filament\ItemTypes\ArtType;
use App\Filament\ItemTypes\BanknoteType;
use App\Filament\ItemTypes\BookType;
use App\Filament\ItemTypes\CameraType;
use App\Filament\ItemTypes\CoinType;
use App\Filament\ItemTypes\ComicType;
use App\Filament\ItemTypes\JewelryType;
use App\Filament\ItemTypes\MilitaryCollectibleType;
use App\Filament\ItemTypes\MovieCollectibleType;
use App\Filament\ItemTypes\PenType;
use App\Filament\ItemTypes\PhotoType;
use App\Filament\ItemTypes\PostcardType;
use App\Filament\ItemTypes\RadioType;
use App\Filament\ItemTypes\SportsCollectibleType;
use App\Filament\ItemTypes\StampType;
use App\Filament\ItemTypes\ToyType;
use App\Filament\ItemTypes\VehicleType;
use App\Filament\ItemTypes\VinylRecordType;
use App\Filament\ItemTypes\WatchType;

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
        if (is_null($typeKey) || !isset($this->types[$typeKey]) || is_null($this->types[$typeKey])) {
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
            $translatedTypes[$type] = __('item.type_' . $type);
        }

        // Sort the types alphabetically by their translated value
        asort($translatedTypes);

        return $translatedTypes;
    }
}
