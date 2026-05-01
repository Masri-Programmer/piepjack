<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\V1\DiscountResource;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Lunar\Models\Discount;

class DiscountController extends Controller
{
    /**
     * Display a listing of all discounts.
     */
    public function index(): AnonymousResourceCollection
    {
        $discounts = Discount::active()
            ->usable()
            ->with([
                'discountables',
                'brands',
                'customerGroups',
                'channels',
            ])
            ->orderBy('priority', 'asc')
            ->get();

        return DiscountResource::collection($discounts);
    }
}
