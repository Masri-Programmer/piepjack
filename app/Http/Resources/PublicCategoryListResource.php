<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PublicCategoryListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Extract the name using Lunar's translation helper
        $name = $this->translateAttribute('name');

        return [
            'id' => $this->id,
            'name' => $name,
            'slug' => Str::slug($name),
            'promoted' => true,
            'parent_id' => $this->parent_id,
        ];
    }
}
