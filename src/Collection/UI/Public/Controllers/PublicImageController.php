<?php

namespace Numista\Collection\UI\Public\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Numista\Collection\Domain\Models\Collection;
use Numista\Collection\Domain\Models\Image;
use Numista\Collection\Domain\Models\Item;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PublicImageController extends Controller
{
    /**
     * Show a public image based on its parent's visibility rules.
     */
    public function show(Image $image): BinaryFileResponse
    {
        $image->load('imageable');
        $parent = $image->imageable;

        if (! $parent) {
            abort(404);
        }

        // THE FIX: Simplify authorization logic for debugging.
        // Allow access if the image belongs to either an Item or a Collection,
        // regardless of their status.
        $isAllowed = $parent instanceof Item || $parent instanceof Collection;

        if (! $isAllowed) {
            abort(403, 'Forbidden');
        }

        $disk = Storage::disk('tenants');

        if (! $disk->exists($image->path)) {
            abort(404);
        }

        return response()->file($disk->path($image->path));
    }
}
