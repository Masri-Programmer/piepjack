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
            'user_name' => $this->user ? $this->user->first_name . ' ' . $this->user->last_name : null,
            'total_price' => $this->total->decimal,
            'order_number' => $this->reference, // Lunar uses 'reference' as the human-readable ID
            'order_notes' => $this->notes,
            'status' => $this->status,

            // Ensure we are mapping over 'lines' (Lunar's relationship name for order items)
            'products' => $this->lines->map(function ($line) {
                // 'purchasable' is usually the ProductVariant in Lunar
                $variant = $line->purchasable;
                $product = $variant ? $variant->product : null;

                if (!$product) {
                    return null;
                }

                // Get media from the product level
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

                    // Data structure expected by your Vue 'ProductSmallCard' component
                    'item' => [
                        'id' => $variant->id,
                        'quantity' => $line->quantity,
                        'image_url' => $media ? $media->getUrl() : null,
                        // Get the price they actually paid for this line item
                        'price' => $line->unit_price->decimal,
                        'options' => $variant->values->map(function ($value) {
                            return [
                                'variation_id' => $value->option->id,
                                'name' => $value->option->translate('name'),
                                'value' => $value->translate('name'),
                            ];
                        }),
                        'cartQuantity' => $line->quantity,
                    ],
                ];
            })->filter()->values(), // Filter out any null products and reset array keys

            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}