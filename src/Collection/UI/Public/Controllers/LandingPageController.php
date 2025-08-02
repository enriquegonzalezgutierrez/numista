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
        // THE FIX: Ensure 'image' relationship is eager-loaded for collections.
        $featuredCollections = Collection::query()
            ->with('image')
            ->withCount(['items' => function ($query) {
                $query->where('status', 'for_sale');
            }])
            ->latest()
            ->take(3)
            ->get();

        // THE FIX: Ensure 'images' and 'tenant' relationships are eager-loaded for items.
        $latestItems = Item::query()
            ->where('status', 'for_sale')
            ->with(['images', 'tenant'])
            ->latest('created_at')
            ->take(8)
            ->get();

        return view('public.landing-page', compact('featuredCollections', 'latestItems'));
    }
}
