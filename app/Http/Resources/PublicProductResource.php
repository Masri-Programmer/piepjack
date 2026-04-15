<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PublicProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $name = $this->translateAttribute('name');
        $media = $this->getFirstMedia('primary') ?: $this->getFirstMedia('images');
        $collection = $this->collections->first();

        return [
            'id' => $this->id,
            'name' => $name,
            'slug' => Str::slug($name),
            'image_url' => $media ? $media->getUrl() : null,
            'image_mime' => $media ? $media->mime_type : null,
            'image_size' => $media ? $media->size : null,
            'images' => $this->media->filter(fn ($m) => str_contains($m->mime_type, 'image'))->map(fn ($m) => $m->getUrl())->values()->toArray(),
            'description' => $this->translateAttribute('description'),
            'category_id' => $collection ? $collection->id : null,
            'category_name' => $collection ? $collection->translateAttribute('name') : null,
            'items' => PublicVariantResource::collection($this->whenLoaded('variants')),
            'price' => $this->variants->first()?->prices->first()?->price->decimal,
            'formatted_price' => $this->variants->first()?->prices->first()?->price->formatted,
        ];
    }
}
