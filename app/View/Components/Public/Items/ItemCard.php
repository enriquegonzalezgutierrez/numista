<?php

// app/View/Components/Public/Items/ItemCard.php

namespace App\View\Components\Public\Items;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Numista\Collection\Domain\Models\Item; // Import the Item model

class ItemCard extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(
        public Item $item // Define the 'item' property
    ) {}

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.public.items.item-card');
    }
}
