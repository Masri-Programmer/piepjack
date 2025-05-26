<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductItemListResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'image_url' => $this->image,
            'image_mime' => $this->image_mime,
            'image_size' => $this->image_size,
            'active' => (bool) $this->active,
            'price' => $this->price,
        ];
    }
}
