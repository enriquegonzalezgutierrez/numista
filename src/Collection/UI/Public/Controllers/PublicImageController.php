<?php

// src/Collection/UI/Public/Controllers/PublicImageController.php

namespace Numista\Collection\UI\Public\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Numista\Collection\Domain\Models\Image;
use Numista\Collection\Domain\Models\Item;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PublicImageController extends Controller
{
    /**
     * Show a public image only if its parent item is for sale.
     * This prevents direct access to private images.
     */
    public function show(Image $image): StreamedResponse
    {
        // Eager load the "imageable" relationship, which is the Item model.
        $image->load('imageable');

        // Security Check:
        // 1. Ensure the image belongs to an Item.
        // 2. Ensure the Item's status is 'for_sale'.
        if (! ($image->imageable instanceof Item) || $image->imageable->status !== 'for_sale') {
            abort(404);
        }

        $disk = Storage::disk('tenants');

        if (! $disk->exists($image->path)) {
            abort(404);
        }

        // Use Laravel's response() method to correctly stream the file
        // with the proper headers (MIME type, etc.).
        return $disk->response($image->path);
    }
}
