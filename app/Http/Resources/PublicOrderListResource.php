<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Lunar\Models\ProductVariant;

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
            'order_number' => $this->reference, // This is the "Order Number"
            'customer_reference' => $this->customer_reference, // "Kundenreferenz"
            'status' => $this->status,
            'created_at' => $this->created_at->toDateTimeString(),
            'order_notes' => $this->notes,

            // Totals & Pricing
            'totals' => [
                'sub_total' => $this->sub_total->decimal,
                'shipping_total' => $this->shipping_total->decimal,
                'discount_total' => $this->discount_total->decimal,
                'tax_total' => $this->tax_total->decimal,
                'total_price' => $this->total->decimal,
            ],
            'tracking' => [
                'number' => $this->tracking_number ?? $this->meta['tracking_number'] ?? null,
                'url' => $this->label_url ?? $this->meta['tracking_url'] ?? $this->meta['label_url'] ?? null,
                'carrier' => $this->meta['carrier'] ?? 'Sendcloud',
            ],
            // Addresses
            'billing_address' => $this->billingAddress,
            'shipping_address' => $this->shippingAddress,

            // Products (Filtering out shipping lines as we did before)
            'products' => $this->lines->map(function ($line) {
                if ($line->purchasable_type !== ProductVariant::class) {
                    return null;
                }

                $variant = $line->purchasable;
                $product = $variant->product;

                if (! $product) {
                    return null;
                }

                $media = $product->getFirstMedia('primary') ?: $product->getFirstMedia('images');

                return [
                    'id' => $product->id,
                    'name' => $product->translateAttribute('name'),
                    'image_url' => $media ? $media->getUrl() : null,
                    'item' => [
                        'id' => $variant->id,
                        'quantity' => $line->quantity,
                        'price' => $line->unit_price->decimal,
                        'sub_total' => $line->sub_total->decimal, // Price before line discounts
                        'total' => $line->total->decimal, // Final line price
                        'options' => $variant->values->map(fn ($v) => [
                            'name' => $v->option->translate('name'),
                            'value' => $v->translate('name'),
                        ]),
                    ],
                ];
            })->filter()->values(),
        ];
    }
}
