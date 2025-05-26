<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'image_url' => $this->image,
            'image_mime' => $this->image_mime,
            'image_size' => $this->image_size,
            'active' => (bool) $this->active,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'updated_at' => (new DateTime($this->updated_at))->format('Y-m-d H:i:s'),
        ];
    }
}
