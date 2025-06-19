<?php
// app/Filament/ItemStatuses/ItemStatus.php
namespace Numista\Collection\UI\Filament\ItemStatuses;

/**
 * Defines the contract for an item status.
 * In a more complex scenario, this could define methods for transitions, colors, etc.
 * For now, it's a marker interface to ensure type consistency.
 */
interface ItemStatus
{
    // Currently, this interface is just for structure.
    // We could add methods later, e.g., public static function getBadgeColor(): string;
}