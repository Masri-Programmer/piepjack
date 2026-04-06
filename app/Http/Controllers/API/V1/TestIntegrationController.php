<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmation;
use App\Models\User;
use App\Services\SendcloudService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Lunar\Models\Order;

class TestIntegrationController extends Controller
{
    protected SendcloudService $sendcloud;

    public function __construct(SendcloudService $sendcloud)
    {
        $this->sendcloud = $sendcloud;
    }

    /**
     * Test SMTP Email sending.
     */
    public function testEmail(): JsonResponse
    {
        try {
            // Find any order to use as dummy data
            $order = Order::latest()->first();
            
            if (!$order) {
                return response()->json(['message' => 'No order found in DB to use for template testing. Please create at least one order manually or via seeder.'], 404);
            }

            $order->load(['shippingAddress', 'user', 'lines.purchasable.product']);

            $orderData = [
                'order' => $order,
                'user' => $order->user,
                'address' => $order->shippingAddress,
                'products' => [], // Empty for simple test
                'total' => $order->total->decimal,
            ];

            Mail::to(config('mail.from.address'))->send(new OrderConfirmation($orderData));

            return response()->json(['message' => 'Email sent successfully to ' . config('mail.from.address')]);
        } catch (Exception $e) {
            Log::error('Test Email Failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Email failed: ' . $e->getMessage(),
                'smtp_user' => config('mail.mailers.smtp.username'),
                'smtp_host' => config('mail.mailers.smtp.host'),
            ], 500);
        }
    }

    /**
     * Test Sendcloud API connection.
     */
    public function testSendcloud(): JsonResponse
    {
        $testData = [
            'name' => 'Test User',
            'address' => 'Test Street 1',
            'city' => 'Berlin',
            'zip' => '10115',
            'country_code' => 'DE',
            'email' => 'test@example.com',
        ];

        try {
            // Attempt to create a parcel (using weight 1.0kg and a dummy method ID)
            $result = $this->sendcloud->createParcel($testData, 1.0, 8);
            
            return response()->json([
                'message' => 'Sendcloud connection successful',
                'result' => $result
            ]);
        } catch (Exception $e) {
            Log::error('Test Sendcloud Failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Sendcloud failed: ' . $e->getMessage(),
                'public_key' => config('services.sendcloud.public_key'),
            ], 500);
        }
    }
}
