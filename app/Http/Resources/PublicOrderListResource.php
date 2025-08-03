<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicOrderListResource extends JsonResource
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
            'user_name' => $this->user->first_name . ' ' . $this->user->last_name,
            'total_price' => $this->total_price,
            'order_number' => $this->order_number,
            'order_notes' => $this->order_notes,
            'status' => $this->status,
            'products' => $this->products->map(function ($product) {
                $productItem = $product->productItem;

                return [
                    'id' => $productItem->product->id,
                    'name' => $productItem->product->name,
                    'slug' => $productItem->product->slug,
                    'image_url' => $productItem->product->image,
                    'image_mime' => $productItem->product->image_mime,
                    'image_size' => $productItem->product->image_size,
                    'description' => $productItem->product->description,
                    'category_id' => $productItem->product->category_id,
                    'category_name' => $productItem->product->category->name,
                    'item' => [
                        'id' => $productItem->id,
                        'quantity' => $productItem->quantity,
                        'image_url' => $productItem->image,
                        'image_mime' => $productItem->image_mime,
                        'image_size' => $productItem->image_size,
                        'price' => $productItem->price,
                        'options' => $productItem->options->map(function ($option) {
                            return [
                                'variation_id' => $option->variation->id,
                                'value' => $option->value,
                                'name' => $option->variation->name,
                                'variation_option_id' => $option->id,
                            ];
                        }),
                        'cartQuantity' => $product->quantity,
                    ],
                ];
            }),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
