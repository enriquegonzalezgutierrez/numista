<?php

// src/Collection/UI/Public/Controllers/PublicItemController.php

namespace Numista\Collection\UI\Public\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Numista\Collection\Application\Items\ItemFinder;
use Numista\Collection\Domain\Models\Category; // THE FIX: Re-add the Category model import
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\SharedAttribute;

class PublicItemController extends Controller
{
    public function index(Request $request, ItemFinder $itemFinder): View|\Illuminate\Http\Response
    {
        $filters = collect($request->all())->filter()->all();
        if (isset($filters['attributes'])) {
            $filters['attributes'] = array_filter($filters['attributes']);
            if (empty($filters['attributes'])) {
                unset($filters['attributes']);
            }
        }

        $items = $itemFinder->forMarketplace($filters);

        if ($request->wantsJson()) {
            $html = view('public.items.partials._items-grid', ['items' => $items])->render();

            return response($html)->header('X-Next-Page-Url', $items->nextPageUrl());
        }

        // THE FIX: Query for global categories that have at least one item for sale.
        $categories = Category::query()
            ->whereHas('items', fn ($q) => $q->where('status', 'for_sale'))
            ->orderBy('name')
            ->get();

        $filterableAttributes = SharedAttribute::query()
            ->where('is_filterable', true)
            ->with('options')
            ->orderBy('name')
            ->get();

        // THE FIX: Pass the 'categories' variable back to the view.
        return view('public.items.index', compact('items', 'categories', 'filterableAttributes'));
    }

    public function show(Item $item): View
    {
        if ($item->status !== 'for_sale') {
            abort(404);
        }

        $item->load(['images', 'tenant', 'categories', 'attributes.options']);

        return view('public.items.show', compact('item'));
    }
}
