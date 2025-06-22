<?php
// app/Filament/ItemTypes/ItemType.php

namespace Numista\Collection\UI\Filament\ItemTypes;

use Filament\Forms\Components\Component;

/**
 * Defines the contract for an item type's specific form fields.
 */
interface ItemType
{
    /**
     * Get the specific form components for this item type.
     *
     * @return array<int, Component>
     */
    public static function getFormComponents(): array;
}
