<?php

namespace App\Http\Resources;

use App\Models\ProductConfiguration;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductItemResource extends JsonResource
{
    public function toArray($request)
    {
        $_options = ProductConfiguration::with('variationOption.variation')->where('product_item_id', $this->id)->get();

        $options = [];

        foreach ($_options as $_option) {
            $options[$_option->variationOption->variation->id] = ['variation_id' => $_option->variationOption->variation->id, 'value' => $_option->variationOption->value, 'name' => $_option->variationOption->variation->name, 'variation_option_id' => $_option->variation_option_id];
        }

        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'name' => $this->product->name,
            'image_url' => $this->image,
            'image_mime' => $this->image_mime,
            'image_size' => $this->image_size,
            'active' => (bool) $this->active,
            'price' => $this->price,
            'options' => $options,
        ];
    }
}
