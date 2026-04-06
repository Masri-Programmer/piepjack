<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $price = $this->prices->first() ? $this->prices->first()->price->decimal : null;

        // FIX: Map Lunar's option values into an array of objects for Vue
        $options = $this->values->map(function ($value) {
            return [
                'name' => $value->option->translate('name'),
                'value' => $value->translate('name'),
            ];
        });

        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'stock' => $this->stock,
            'quantity' => $this->stock, // Alias for compatibility with shop.js
            'price' => $price,
            'formatted_price' => $this->prices->first()?->price->formatted,
            'options' => $options,
        ];
    }
}
