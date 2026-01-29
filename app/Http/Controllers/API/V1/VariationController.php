<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\VariationResource;
use App\Models\Category;
use App\Models\Variation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VariationController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Variation::filter(request()->only(['category_id', 'search']), ['name'])
            ->with('category', 'options')
            ->sort([request('sort_field', 'created_at') => request('sort_direction', 'desc')])
            ->paginateResults(request('per_page', 10));

        return VariationResource::collection($query)->response();
    }


    public function categoryVariations(Category $category): JsonResponse
    {
        return VariationResource::collection(Variation::with('options')->where('category_id', $category->id)->get())->response();
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
        ]);

        $variation = Variation::create($validated);

        return response()->json($variation, 201);
    }

    public function show(Variation $variation): JsonResponse
    {
        return (new VariationResource($variation->load('category', 'options')))->response();
    }

    public function update(Request $request, Variation $variation): JsonResponse
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
        ]);

        $variation->update($validated);

        return response()->json($variation);
    }

    public function destroy(Variation $variation): JsonResponse
    {
        $variation->delete();

        return response()->json(null, 204);
    }
}
