<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Lunar\FieldTypes\Text;
use Lunar\FieldTypes\TranslatedText;
use Lunar\Models\Channel;
use Lunar\Models\Collection;
use Lunar\Models\CollectionGroup;

class CollectionSeeder extends AbstractSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $collections = $this->getSeedData('collections');

        $collectionGroup = CollectionGroup::first();

        $channel = Channel::getDefault();

        DB::transaction(function () use ($collections, $collectionGroup, $channel) {
            foreach ($collections as $collection) {
                $coll = Collection::create([
                    'collection_group_id' => $collectionGroup->id,
                    'attribute_data' => [
                        'name' => new TranslatedText([
                            'en' => new Text($collection->name),
                        ]),
                        'description' => new TranslatedText([
                            'en' => new Text($collection->description),
                        ]),
                    ],
                ]);

                if ($channel) {
                    $coll->channels()->sync([
                        $channel->id => [
                            'enabled' => true,
                            'starts_at' => now(),
                        ],
                    ]);
                }
            }
        });
    }
}
