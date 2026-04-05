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
        $collections = Collection::all();

        return PublicCategoryListResource::collection($collections)->response();
    }
}
