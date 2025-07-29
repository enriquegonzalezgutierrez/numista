<?php

namespace Numista\Collection\UI\Public\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Numista\Collection\Domain\Models\Collection;
use Numista\Collection\Domain\Models\Item;

class LandingPageController extends Controller
{
    public function __invoke(): View
    {
        // THE FIX: Constrain the 'items_count' to only include items that are for sale.
        $featuredCollections = Collection::query()
            ->with('image')
            ->withCount(['items' => function ($query) {
                $query->where('status', 'for_sale');
            }])
            ->latest()
            ->take(3)
            ->get();

        $latestItems = Item::query()
            ->where('status', 'for_sale')
            ->with(['images', 'tenant'])
            ->latest('created_at')
            ->take(8)
            ->get();

        return view('public.landing-page', compact('featuredCollections', 'latestItems'));
    }
}
