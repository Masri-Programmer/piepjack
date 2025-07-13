<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        $returnItems = $this->returning ? $this->returning->items->pluck('quantity', 'order_product_id') : collect();

        return [
            'id' => $this->id,
            'total_price' => $this->total_price,
            'status' => $this->status,
            'user' => new UserResource($this->whenLoaded('user')),
            'shipping_address' => new AddressResource($this->whenLoaded('shippingAddress')),
            'billing_address' => new AddressResource($this->whenLoaded('billingAddress')),
            'created_at' => $this->created_at,
            'order_number' => $this->order_number,
            'updated_at' => $this->updated_at,
            'returning' => $this->returning,
            'products' => $this->products ? $this->products->map(function ($product) use ($returnItems) {
                return [
                    'product_item_id' => $product->product_item_id,
                    'name' => $product->productItem->product->name ?? null,
                    'price_per_item' => $product->price_per_item,
                    'image' => $product->productItem->product->image,
                    'quantity' => $product->quantity,
                    'returned_quantity' => $returnItems[$product->product_item_id] ?? 0,
                    'return_status' => $this->returning?->status,
                    'return_reason' => $this->returning?->reason,
                    'return_id' => $this->returning?->id,
                    'options' => $product->productItem->options->map(function ($option) {
                        return [
                            'id' => $option->id,
                            'value' => $option->value,
                            'name' => $option->variation->name,
                        ];
                    }),
                ];
            }) : [],
        ];
    }
}
