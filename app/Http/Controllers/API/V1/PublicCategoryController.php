<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicCategoryListResource;
use Illuminate\Http\JsonResponse;
use Lunar\Models\Collection;

class PublicCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Collection::query();

        if ($search = request('search')) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(JSON_EXTRACT(attribute_data, "$.name.value.en")) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(attribute_data, "$.name.value.de")) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(attribute_data, "$.description.value.en")) LIKE ?', ["%{$search}%"])
                    ->orWhereRaw('LOWER(JSON_EXTRACT(attribute_data, "$.description.value.de")) LIKE ?', ["%{$search}%"]);
            });
        }

        $collections = $query->get();

        return PublicCategoryListResource::collection($collections)->response();
    }
}
