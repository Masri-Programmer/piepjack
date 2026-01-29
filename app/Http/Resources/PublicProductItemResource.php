<?php

namespace App\Http\Resources;

use App\Models\ProductConfiguration;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicProductItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $_options = ProductConfiguration::with('variationOption.variation')->where('product_item_id', $this->id)->get();

        $options = [];

        foreach ($_options as $_option) {
            $options[$_option->variationOption->variation->id] = ['variation_id' => $_option->variationOption->variation->id, 'value' => $_option->variationOption->value, 'name' => $_option->variationOption->variation->name, 'variation_option_id' => $_option->variation_option_id];
        }

        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'image_url' => $this->image,
            'image_mime' => $this->image_mime,
            'image_size' => $this->image_size,
            'price' => $this->price,
            'options' => $options,
        ];
    }
}
