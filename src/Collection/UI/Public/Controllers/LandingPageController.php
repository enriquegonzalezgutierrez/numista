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
        // THE FIX: Eager load the 'image' relationship for collections
        $featuredCollections = Collection::query()
            ->with('image')
            ->withCount('items')
            ->latest()
            ->take(3)
            ->get();

        // Eager load the main image for each item for efficiency
        $latestItems = Item::query()
            ->where('status', 'for_sale')
            ->with(['images', 'tenant']) // images relation already fetches all images
            ->latest('created_at')
            ->take(8)
            ->get();

        return view('public.landing-page', compact('featuredCollections', 'latestItems'));
    }
}
