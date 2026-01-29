<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\VariationOptionResource;
use App\Models\VariationOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VariationOptionController extends Controller
{
    public function index(): JsonResponse
    {
        return VariationOptionResource::collection(VariationOption::with('variation')->get())->response();
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'variation_id' => 'required|exists:variations,id',
            'value' => 'required|string|max:255',
        ]);

        $option = VariationOption::create($validated);

        return response()->json($option, 201);
    }

    public function show(VariationOption $variationOption): JsonResponse
    {
        return (new VariationOptionResource($variationOption->load('variation')))->response();
    }

    public function update(Request $request, VariationOption $variationOption): JsonResponse
    {
        $validated = $request->validate([
            'variation_id' => 'required|exists:variations,id',
            'value' => 'required|string|max:255',
        ]);

        $variationOption->update($validated);

        return response()->json($variationOption);
    }

    public function destroy(VariationOption $variationOption): JsonResponse
    {
        $variationOption->delete();

        return response()->json(null, 204);
    }
}
