<?php

namespace App\Mcp\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;
use Lunar\Models\Discount;
use Lunar\Models\Order;

#[Description('Returns an overview of the store, including order counts, revenue, and active discounts.')]
class StoreOverview extends Tool
{
    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $totalOrders = Order::count();
        $recentOrdersCount = Order::where('created_at', '>=', now()->subDay())->count();

        $totalRevenue = Order::all()->sum('total');
        $revenueFormatted = number_format($totalRevenue / 100, 2).' EUR';

        // Check for active Lunar discounts
        $discounts = Discount::all()->map(fn ($d) => $d->name.' ('.$d->handle.')')->join(', ') ?: 'None';

        $output = "Store Status Overview:\n";
        $output .= "- Total Orders: {$totalOrders}\n";
        $output .= "- Orders in last 24h: {$recentOrdersCount}\n";
        $output .= "- Total Lifetime Revenue: {$revenueFormatted}\n";
        $output .= "- Active Lunar Discounts: {$discounts}\n";
        $output .= "- Custom Logic: Special 'pickup' 10 EUR discount is active via StoreDiscountModifier.";

        return Response::text($output);
    }

    /**
     * Get the tool's input schema.
     *
     * @return array<string, JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [];
    }
}
