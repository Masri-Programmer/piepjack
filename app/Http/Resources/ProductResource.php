<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'image_url' => $this->image,
            'image_mime' => $this->image_mime,
            'image_size' => $this->image_size,
            'active' => (bool) $this->active,
            'description' => $this->description,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'items' => ProductItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
