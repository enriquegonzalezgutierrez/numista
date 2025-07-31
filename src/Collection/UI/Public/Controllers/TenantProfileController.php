<?php

// src/Collection/UI/Public/Controllers/TenantProfileController.php

namespace Numista\Collection\UI\Public\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Numista\Collection\Application\Items\ItemFinder;
use Numista\Collection\Domain\Models\SharedAttribute;
use Numista\Collection\Domain\Models\Tenant;

class TenantProfileController extends Controller
{
    /**
     * Display the specified tenant's public profile and their items for sale.
     */
    public function __invoke(Tenant $tenant, ItemFinder $itemFinder, Request $request): View|\Illuminate\Http\Response
    {
        // Get filters from the request, just like in PublicItemController
        $filters = collect($request->all())->filter()->all();
        if (isset($filters['attributes'])) {
            $filters['attributes'] = array_filter($filters['attributes']);
            if (empty($filters['attributes'])) {
                unset($filters['attributes']);
            }
        }

        // Pass the filters to the item finder method
        $items = $itemFinder->forTenantProfile($tenant, $filters);

        if ($request->wantsJson()) {
            $html = view('public.items.partials._items-grid', ['items' => $items])->render();

            return response($html)->header('X-Next-Page-Url', $items->nextPageUrl());
        }

        // Get filterable attributes to pass to the view, just like in PublicItemController
        $filterableAttributes = SharedAttribute::query()
            ->where('is_filterable', true)
            ->whereHas('itemTypes.items', function ($query) use ($tenant) {
                // Only show attributes that are actually used by items from THIS tenant
                $query->where('tenant_id', $tenant->id)->where('status', 'for_sale');
            })
            ->with('options')
            ->orderBy('name')
            ->get();

        return view('public.tenants.show', compact('tenant', 'items', 'filterableAttributes'));
    }
}
