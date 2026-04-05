<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PublicProductListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // 1. Get the primary image via Spatie Media Library
        $media = $this->getFirstMedia('primary') ?: $this->getFirstMedia('images');

        // 2. Map Lunar's Collections to your concept of a "Category"
        $collection = $this->collections->first();

        return [
            'id' => $this->id,
            'name' => $this->translateAttribute('name'),
            'slug' => Str::slug($this->translateAttribute('name')),

            // 3. Image handling
            'image_url' => $media ? $media->getUrl() : null,
            'image_mime' => $media ? $media->mime_type : null,
            'image_size' => $media ? $media->size : null,

            'description' => $this->translateAttribute('description'),

            // 4. Map the first Collection to the category fields your frontend expects
            'category_id' => $collection ? $collection->id : null,
            'category_name' => $collection ? $collection->translateAttribute('name') : null,

            // 5. Lunar uses "variants" instead of "items"
            // We keep the 'items' key so your Vue frontend doesn't need to change!
            'items' => PublicVariantResource::collection($this->whenLoaded('variants')),
        ];
    }
}
