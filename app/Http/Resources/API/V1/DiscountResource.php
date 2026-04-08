<?php

namespace App\Http\Resources\API\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Lunar\Facades\StorefrontSession;

class DiscountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $currencyCode = StorefrontSession::getCurrency()->code;

        return [
            'id' => $this->id,
            'name' => $this->name,
            'handle' => $this->handle,
            'coupon_code' => $this->coupon,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'status' => $this->status, // active, pending, expired, scheduled
            'usage' => [
                'total_uses' => $this->uses,
                'max_uses' => $this->max_uses,
                'max_uses_per_user' => $this->max_uses_per_user,
            ],
            'type' => $this->getTypeName(),
            'priority' => $this->priority,
            'stop' => (bool) $this->stop,
            'config' => [
                'is_fixed' => $this->data['fixed_value'] ?? false,
                'percentage' => $this->data['percentage'] ?? null,
                'fixed_amount' => $this->data['fixed_values'][$currencyCode] ?? $this->data['fixed_amount'] ?? null,
                'min_prices' => $this->data['min_prices'] ?? [],
            ],
            'restrictions' => [
                'discountables' => $this->whenLoaded('discountables', function () {
                    return $this->discountables->map(function ($discountable) {
                        return [
                            'id' => $discountable->discountable_id,
                            'type' => class_basename($discountable->discountable_type),
                            'restriction_type' => $discountable->type, // condition, reward, exclusion, limitation
                        ];
                    });
                }),
                'brands' => $this->whenLoaded('brands', function () {
                    return $this->brands->pluck('id');
                }),
                'customer_groups' => $this->whenLoaded('customerGroups', function () {
                    return $this->customerGroups->pluck('id');
                }),
            ],
        ];
    }

    /**
     * Helper to get a human-readable type from the class string.
     */
    protected function getTypeName(): string
    {
        return match ($this->type) {
            'Lunar\DiscountTypes\AmountOff' => 'amount_off',
            'Lunar\DiscountTypes\PercentageOff' => 'percentage_off',
            'Lunar\DiscountTypes\BuyXGetY' => 'buy_x_get_y',
            default => class_basename($this->type),
        };
    }
}
