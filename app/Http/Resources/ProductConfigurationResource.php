<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductConfigurationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'product_item_id' => $this->product_item_id,
            'variation_option_id' => $this->variation_option_id,
            'variation_option' => new VariationOptionResource($this->whenLoaded('variationOption')),
        ];
    }
}
