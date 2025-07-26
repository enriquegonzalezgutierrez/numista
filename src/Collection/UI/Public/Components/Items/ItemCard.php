<?php

// src/Collection/UI/Public/Components/Items/ItemCard.php

namespace Numista\Collection\UI\Public\Components\Items;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Numista\Collection\Domain\Models\Item;

class ItemCard extends Component
{
    /**
     * Create a new component instance.
     * The component receives an Item model instance.
     */
    public function __construct(
        public Item $item
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.public.items.item-card');
    }
}
