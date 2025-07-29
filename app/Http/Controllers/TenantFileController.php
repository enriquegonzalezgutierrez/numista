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

        // --- THE FIX: Simplified and Correct Authorization ---

        // 1. Is the resource publicly accessible?
        $isPubliclyAccessible = ($parent instanceof Item && $parent->status === 'for_sale') || $parent instanceof Collection;

        // 2. Can the current user access the tenant?
        $canUserAccessTenant = $user && $user->canAccessTenant($parent->tenant);

        // 3. Deny if not public AND user cannot access the tenant.
        if (! $isPubliclyAccessible && ! $canUserAccessTenant) {
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
