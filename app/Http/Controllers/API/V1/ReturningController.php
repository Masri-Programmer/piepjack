<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Resources\ReturningListResource;
use App\Http\Requests\Returning\StoreReturningRequest;
use App\Http\Requests\Returning\UpdateReturningRequest;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Returning;
use App\Models\ReturnItem;

class ReturningController extends Controller
{
    public function index()
    {
        $query = Returning::filter(request()->only(['status', 'search']), ['id', 'order_id', 'reason'])
            ->sort([request('sort_field', 'created_at') => request('sort_direction', 'desc')])
            ->paginateResults(request('per_page', 10));

        return ReturningListResource::collection($query)
            ->additional(['meta' => ['statuses' => Returning::getStatusOptions()]]);
    }

    public function show($id)
    {
        $returning = Returning::with(['order', 'items.orderProduct'])->findOrFail($id);

        return response()->json($returning);
    }

    public function store(StoreReturningRequest $request)
    {
        $validated = $request->validated();

        $order = Order::findOrFail($validated['order_id']);

        if ($order->status !== 'paid') {
            return response()->json([
                'message' => 'Returns can only be created for paid orders.',
            ], 422);
        }

        $returning = Returning::create([
            'order_id' => $validated['order_id'],
            'status' => $validated['status'],
            'reason' => $validated['reason'] ?? null,
        ]);

        $this->processReturnItems($returning, $validated['items']);

        return response()->json([
            'message' => 'Return record created successfully',
            'data' => $returning->load('items'),
        ], 201);
    }

    public function update(UpdateReturningRequest $request, $id)
    {
        $returning = Returning::findOrFail($id);

        $validated = $request->validated();

        $returning->update($validated);

        if (isset($validated['items'])) {
            $this->processReturnItems($returning, $validated['items']);
        }

        return response()->json([
            'message' => 'Return record updated successfully',
            'data' => $returning->load(['items.orderProduct']),
        ]);
    }

    public function destroy($id)
    {
        $returning = Returning::with('items')->findOrFail($id);

        try {
            $returning->items()->delete();
            $returning->delete();

            return response()->json([
                'message' => 'Return record and associated items deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to delete return record: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function processReturnItems(Returning $returning, array $items)
    {
        foreach ($items as $item) {
            $existingReturnItem = ReturnItem::where('order_product_id', $item['id'])
                ->where('return_id', '!=', $returning->id)
                ->first();

            if ($existingReturnItem) {
                throw new \Exception("Product with ID {$item['id']} has already been returned.");
            }

            $returnItem = ReturnItem::updateOrCreate(
                ['return_id' => $returning->id, 'order_product_id' => $item['id']],
                ['quantity' => $item['cartQuantity']]
            );
        }
    }
}
