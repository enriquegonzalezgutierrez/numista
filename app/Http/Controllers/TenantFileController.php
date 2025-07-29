<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Numista\Collection\Domain\Models\Collection;
use Numista\Collection\Domain\Models\Image;
use Numista\Collection\Domain\Models\Item;
use Numista\Collection\Domain\Models\Tenant;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TenantFileController extends Controller
{
    /**
     * Serves an image after performing authorization checks.
     */
    public function showImage(Image $image): BinaryFileResponse
    {
        /** @var User|null $user */
        $user = Auth::user();
        $parent = $image->imageable;

        if (! $parent) {
            abort(404);
        }

        // --- THE FIX: Expanded Authorization Logic ---

        // Condition 1: Is the resource publicly browsable?
        $isPubliclyAccessible = ($parent instanceof Item && $parent->status === 'for_sale') || $parent instanceof Collection;

        // Condition 2: Can the current authenticated user access the tenant (e.g., is an admin)?
        $canUserAccessTenant = $user && $user->canAccessTenant($parent->tenant);

        // Condition 3: Has the current authenticated user purchased this item?
        $userHasPurchasedItem = false;
        if ($user && $parent instanceof Item) {
            $userHasPurchasedItem = $user->orders()->whereHas('items', function ($query) use ($parent) {
                $query->where('item_id', $parent->id);
            })->exists();
        }

        // Deny access if NONE of the conditions are met.
        if (! $isPubliclyAccessible && ! $canUserAccessTenant && ! $userHasPurchasedItem) {
            abort(403, 'You do not have permission to access this file.');
        }

        return $this->serveFile($image->path);
    }

    /**
     * Serves a generic file from tenant storage (for authenticated users).
     */
    public function showFile(string $path): BinaryFileResponse
    {
        /** @var User|null $user */
        $user = Auth::user();

        if (! $user) {
            abort(403, 'Forbidden');
        }

        if (! preg_match('/^tenant-(\d+)\//', $path, $matches)) {
            abort(404, 'Invalid file path format.');
        }

        $tenantId = $matches[1];
        $tenant = Tenant::find($tenantId);

        if (! $tenant || ! $user->canAccessTenant($tenant)) {
            abort(403, 'You do not have permission to access this file.');
        }

        return $this->serveFile($path);
    }

    /**
     * Helper to stream the file from storage.
     */
    private function serveFile(string $path): BinaryFileResponse
    {
        $disk = Storage::disk('tenants');

        if (! $disk->exists($path)) {
            abort(404);
        }

        $fullPath = $disk->path($path);
        $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';

        return response()->file($fullPath, ['Content-Type' => $mimeType]);
    }
}
