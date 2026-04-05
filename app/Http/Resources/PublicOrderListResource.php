<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class PublicOrderListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_name' => $this->user ? $this->user->first_name.' '.$this->user->last_name : null,
            // Lunar stores totals in cents and casts them to a Price object.
            // ->decimal gives you the clean float (e.g., 74.95)
            'total_price' => $this->total->decimal,
            // Lunar uses 'reference' for the public order number
            'order_number' => $this->reference,
            'order_notes' => $this->notes,
            'status' => $this->status,

            // Map over Lunar's OrderLines
            'products' => $this->lines->map(function ($line) {
                // The 'purchasable' is the ProductVariant that was bought
                $variant = $line->purchasable;
                $product = $variant->product;

                $media = $product->getFirstMedia('primary') ?: $product->getFirstMedia('images');
                $collection = $product->collections->first();

                return [
                    'id' => $product->id,
                    'name' => $product->translateAttribute('name'),
                    'slug' => Str::slug($product->translateAttribute('name')),
                    'image_url' => $media ? $media->getUrl() : null,
                    'image_mime' => $media ? $media->mime_type : null,
                    'image_size' => $media ? $media->size : null,
                    'description' => $product->translateAttribute('description'),
                    'category_id' => $collection ? $collection->id : null,
                    'category_name' => $collection ? $collection->translateAttribute('name') : null,

                    // Maintain your frontend's 'item' structure for the variant
                    'item' => [
                        'id' => $variant->id,
                        'quantity' => $line->quantity,
                        'image_url' => $media ? $media->getUrl() : null,
                        // Get the price they actually paid for this line item
                        'price' => $line->unit_price->decimal,

                        'options' => $variant->values->map(function ($value) {
                            return [
                                'variation_id' => $value->option->id,
                                'value' => $value->translate('name'),
                                'name' => $value->option->translate('name'),
                                'variation_option_id' => $value->id,
                            ];
                        }),
                        'cartQuantity' => $line->quantity,
                    ],
                ];
            }),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
