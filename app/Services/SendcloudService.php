<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendcloudService
{
    protected string $publicKey;

    protected string $secretKey;

    protected string $baseUrl = 'https://panel.sendcloud.sc/api/v2';

    public function __construct()
    {
        $this->publicKey = config('services.sendcloud.public_key');
        $this->secretKey = config('services.sendcloud.secret_key');
    }

    /**
     * Get available shipping methods from Sendcloud.
     */
    public function getShippingMethods(): array
    {
        return cache()->remember('sendcloud_shipping_methods', 86400, function () {
            try {
                $response = Http::withBasicAuth($this->publicKey, $this->secretKey)
                    ->get("{$this->baseUrl}/shipping_methods");

                if ($response->successful()) {
                    return $response->json('shipping_methods') ?? [];
                }

                Log::error('Sendcloud Get Shipping Methods API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return [];
            } catch (\Exception $e) {
                Log::error('Sendcloud Get Shipping Methods Exception: '.$e->getMessage());

                return [];
            }
        });
    }

    /**
     * Create a parcel in Sendcloud and generate the label.
     *
     * @param  array  $customerData  The shipping address and contact info.
     * @param  float  $weight  The total weight of the order in kg.
     * @param  int  $shippingMethodId  The Sendcloud ID for the shipping method.
     * @param  bool|null  $requestLabel  Whether to request the label (defaults to config).
     * @param  array  $parcelItems  Optional items to include in the parcel for customs/tracking.
     * @return array|null Returns tracking number and label URL, or null on failure.
     */
    public function createParcel(array $customerData, float $weight, int $shippingMethodId, ?bool $requestLabel = null, array $parcelItems = []): ?array
    {
        $requestLabel = $requestLabel ?? config('services.sendcloud.request_label', true);

        try {
            $payload = [
                'parcel' => [
                    'name' => $customerData['name'],
                    'address' => $customerData['address'],
                    'city' => $customerData['city'],
                    'postal_code' => $customerData['zip'],
                    'country' => $customerData['country_code'],
                    'email' => $customerData['email'],
                    'weight' => (string) $weight,
                    'request_label' => $requestLabel,
                    'shipping_method' => $shippingMethodId,
                ],
            ];

            if (! empty($parcelItems)) {
                $payload['parcel']['parcel_items'] = $parcelItems;
            }

            $response = Http::withBasicAuth($this->publicKey, $this->secretKey)
                ->post("{$this->baseUrl}/parcels", $payload);

            if ($response->successful()) {
                $data = $response->json('parcel');

                return [
                    'tracking_number' => $data['tracking_number'] ?? null,
                    'label_url' => $data['documents'][0]['link'] ?? null,
                    'parcel_id' => $data['id'] ?? null,
                ];
            }

            // If we get a 412 (Unprocessable Entity) and we were requesting a label,
            // it likely means "User not allowed to announce". Try again without the label.
            if ($response->status() === 412 && $requestLabel === true) {
                Log::warning('Sendcloud label announcement failed (412). Retrying without label request.');

                return $this->createParcel($customerData, $weight, $shippingMethodId, false, $parcelItems);
            }

            Log::error('Sendcloud API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'parcel_data' => $customerData,
            ]);

            $errorMsg = $response->json('error.message') ?? 'Unknown Sendcloud API error';
            throw new \Exception('Sendcloud Error: '.$errorMsg);
        } catch (\Exception $e) {
            Log::error('Sendcloud Exception: '.$e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }
}
