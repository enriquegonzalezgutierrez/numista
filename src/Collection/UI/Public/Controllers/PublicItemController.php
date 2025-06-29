<?php

namespace Numista\Collection\UI\Public\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Numista\Collection\Domain\Models\Item;

class PublicItemController extends Controller
{
    public function index(): View
    {
        $items = Item::query()
            ->where('status', 'for_sale')
            ->with(['images', 'tenant'])
            ->latest('created_at')
            ->paginate(12);

        return view('public.items.index', compact('items'));
    }

    public function show(Item $item): View
    {
        if ($item->status !== 'for_sale') {
            abort(404);
        }

        $item->load(['images', 'tenant', 'categories']);

        return view('public.items.show', compact('item'));
    }
}
