<?php

namespace Numista\Collection\UI\Public\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Numista\Collection\Domain\Models\Category;
use Numista\Collection\Domain\Models\Item;

class PublicItemController extends Controller
{
    /**
     * Display a paginated list of all items that are for sale,
     * applying any filters from the request.
     */
    public function index(Request $request): View
    {
        // Get all filters from the request
        $filters = $request->only(['search', 'categories']);

        $items = Item::query()
            ->where('status', 'for_sale')
            ->with(['images', 'tenant'])
            ->filter($filters) // Apply our new scope
            ->latest('created_at')
            ->paginate(12);

        // Get categories that have at least one item for sale
        $categories = Category::query()
            ->whereHas('items', fn ($q) => $q->where('status', 'for_sale'))
            ->orderBy('name')
            ->get();

        return view('public.items.index', compact('items', 'categories'));
    }

    /**
     * Display the detail page for a specific item.
     */
    public function show(Item $item): View
    {
        if ($item->status !== 'for_sale') {
            abort(404);
        }

        $item->load(['images', 'tenant', 'categories']);

        return view('public.items.show', compact('item'));
    }
}
