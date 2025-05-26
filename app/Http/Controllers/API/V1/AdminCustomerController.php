<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerListResource;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;

class AdminCustomerController extends Controller
{
    public function index(): JsonResponse
    {
        $filters = request()->only(['active', 'search']);
        $perPage = request('per_page', 10);
        $sortFields = [
            request('sort_field', 'created_at') => request('sort_direction', 'desc'),
        ];
        $query = Customer::filter($filters, ['email'])
            ->sort($sortFields)
            ->paginateResults($perPage);

        $totalActive = Customer::countByStatus(true);
        $totalInactive = Customer::countByStatus(false);

        $resource = CustomerListResource::collection($query);

        return $resource->additional([
            'meta' => [
                'total_active_customers' => $totalActive,
                'total_inactive_customers' => $totalInactive,
            ],
        ])->response();
    }

    public function show(Customer $customer): JsonResponse
    {
        return (new CustomerResource($customer->load('details', 'orders')))->response();
    }

    public function ban(Customer $customer): JsonResponse
    {
        $customer->update(['active' => false]);

        return (new CustomerResource($customer->load('details', 'orders')))->response();
    }

    public function unban(Customer $customer): JsonResponse
    {
        $customer->update(['active' => true]);

        return (new CustomerResource($customer->load('details')))->response();
    }
}
