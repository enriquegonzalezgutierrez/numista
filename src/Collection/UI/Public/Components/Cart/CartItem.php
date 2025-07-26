<?php

namespace Numista\Collection\UI\Public\Components\Cart;

use Illuminate\View\Component;
use Numista\Collection\Domain\Models\Item;

class CartItem extends Component
{
    public function __construct(
        public Item $item,
        public int $quantity
    ) {}

    public function render()
    {
        return view('components.public.cart.cart-item');
    }
}
