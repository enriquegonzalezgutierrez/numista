<?php

// src/Collection/UI/Public/Controllers/PublicItemController.php

namespace Numista\Collection\UI\Public\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Numista\Collection\Domain\Models\Attribute;
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
        // --- NEW LOGIC: Clean up the filters ---
        $filters = collect($request->all())->filter()->all();

        // Specifically handle the 'attributes' array to remove empty values within it
        if (isset($filters['attributes'])) {
            $filters['attributes'] = array_filter($filters['attributes']);
            if (empty($filters['attributes'])) {
                unset($filters['attributes']);
            }
        }

        $items = Item::query()
            ->where('status', 'for_sale')
            ->with(['images', 'tenant', 'attributes'])
            ->filter($filters)
            ->latest('created_at')
            ->paginate(12)
            ->withQueryString(); // <-- withQueryString() is cleaner than appends()

        $categories = Category::query()
            ->whereHas('items', fn ($q) => $q->where('status', 'for_sale'))
            ->orderBy('name')
            ->get();

        $filterableAttributes = Attribute::query()
            ->where('is_filterable', true)
            ->with('values')
            ->orderBy('name')
            ->get();

        return view('public.items.index', compact('items', 'categories', 'filterableAttributes'));
    }

    /**
     * Display the detail page for a specific item.
     */
    public function show(Item $item): View
    {
        if ($item->status !== 'for_sale') {
            abort(404);
        }

        $item->load(['images', 'tenant', 'categories', 'attributes.values']);

        return view('public.items.show', compact('item'));
    }
}
