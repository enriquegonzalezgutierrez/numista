<?php

// app/Http/Controllers/TenantFileController.php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use League\Flysystem\UnableToRetrieveMetadata;

class TenantFileController extends Controller
{
    public function show(string $path)
    {
        // IMPORTANT: Add authorization logic here later!
        // For now, we assume if you have the link, you can see it.
        // In the future, we will check if the logged-in user's tenant
        // matches the tenant ID in the file path.

        $disk = Storage::disk('tenants');

        if (! $disk->exists($path)) {
            abort(404);
        }

        try {
            $mimeType = $disk->mimeType($path);
        } catch (UnableToRetrieveMetadata $e) {
            $mimeType = 'application/octet-stream';
        }

        return response()->file($disk->path($path), ['Content-Type' => $disk->mimeType($path)]);
    }
}
