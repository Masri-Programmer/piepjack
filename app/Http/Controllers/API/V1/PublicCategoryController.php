<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\PublicCategoryListResource;
use App\Models\Category;

class PublicCategoryController extends Controller
{
    public function index()
    {
        $query = Category::query()
            ->where('active', true)
            ->get();

        return PublicCategoryListResource::collection($query)->response();
    }
}
