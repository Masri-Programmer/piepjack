<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class ProductResource extends JsonResource
{
    public function toArray($request)
    {
        // 1. Get the base data
        $baseData = parent::toArray($request);

        // 2. CON MITIGATION: Remove the massive raw JSON column to prevent payload duplication,
        // and hide any internal DB columns you don't want Vue to see.
        $baseData = Arr::except($baseData, [
            'attribute_data',
            'deleted_at',
            'created_at',
            'updated_at',
        ]);

        // 3. Dynamically map translations
        $dynamicAttributes = collect($this->attribute_data)->mapWithKeys(function ($value, $key) {
            return [$key => $this->translateAttribute($key)];
        })->toArray();

        // 4. Format Media
        $mediaData = [
            'primary_image' => $this->media->first(fn ($m) => $m->getCustomProperty('primary') === true)?->getUrl()
                               ?? $this->media->first()?->getUrl(),
            'gallery' => $this->media->map(fn ($m) => [
                'id' => $m->id,
                'url' => $m->getUrl(),
            ]),
        ];

        return array_merge($baseData, $dynamicAttributes, ['media_data' => $mediaData]);
    }
}
