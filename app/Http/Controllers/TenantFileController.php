<?php

// app/Http/Controllers/TenantFileController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Numista\Collection\Domain\Models\Tenant;

class TenantFileController extends Controller
{
    public function show(string $path)
    {
        /** @var User|null $user */
        $user = Auth::user();

        // 1. Must be authenticated
        if (! $user) {
            abort(403, 'Forbidden');
        }

        // 2. Extract tenant ID from the path, e.g., "tenant-1/..." -> 1
        if (! preg_match('/^tenant-(\d+)\//', $path, $matches)) {
            abort(404, 'Invalid file path format.');
        }
        $tenantId = $matches[1];
        $tenant = Tenant::find($tenantId);

        // 3. The tenant must exist and the user must be able to access it
        if (! $tenant || ! $user->canAccessTenant($tenant)) {
            abort(403, 'You do not have permission to access this file.');
        }

        $disk = Storage::disk('tenants');

        if (! $disk->exists($path)) {
            abort(404);
        }

        $fullPath = $disk->path($path);

        $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';

        return response()->file($fullPath, ['Content-Type' => $mimeType]);
    }
}
