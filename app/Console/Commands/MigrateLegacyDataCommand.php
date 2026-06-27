<?php

namespace App\Console\Commands;

use App\Core\Models\Collection;
use App\Models\Hotline;
use App\Models\Office;
use App\Models\Region;
use Illuminate\Console\Command;

class MigrateLegacyDataCommand extends Command
{
    protected $signature = 'cms:migrate-legacy';

    protected $description = 'Migrate legacy Hotlines, Offices, and Regions to the new Schema Engine (Entries)';

    public function handle()
    {
        $this->info('Starting Strangler Migration...');

        $this->migrateHotlines();
        $this->migrateOffices();
        $this->migrateRegions();

        $this->info('Strangler Migration Completed.');
    }

    private function migrateHotlines()
    {
        $collection = Collection::where('slug', 'hotlines')->firstOrFail();
        $blueprint = $collection->blueprints()->firstOrFail();
        $hotlines = Hotline::all();

        $this->info("Migrating {$hotlines->count()} Hotlines...");

        foreach ($hotlines as $hotline) {
            $data = [
                'global' => [
                    'number' => $hotline->number,
                    'is_primary' => $hotline->is_primary,
                    'sort_order' => $hotline->sort_order,
                ],
                'tg' => [
                    'label' => $hotline->getTranslation('label', 'tg', false),
                    'note' => $hotline->getTranslation('note', 'tg', false),
                ],
                'ru' => [
                    'label' => $hotline->getTranslation('label', 'ru', false),
                    'note' => $hotline->getTranslation('note', 'ru', false),
                ],
                'en' => [
                    'label' => $hotline->getTranslation('label', 'en', false),
                    'note' => $hotline->getTranslation('note', 'en', false),
                ],
            ];

            $collection->entries()->create([
                'blueprint_id' => $blueprint->id,
                'status' => 'published',
                'data' => $data,
                'created_at' => $hotline->created_at,
                'updated_at' => $hotline->updated_at,
            ]);
        }
    }

    private function migrateOffices()
    {
        $collection = Collection::where('slug', 'offices')->firstOrFail();
        $blueprint = $collection->blueprints()->firstOrFail();
        $offices = Office::all();

        $this->info("Migrating {$offices->count()} Offices...");

        foreach ($offices as $office) {
            $data = [
                'global' => [
                    'phone' => $office->phone,
                    'email' => $office->email,
                    'is_head' => $office->is_head,
                    'sort_order' => $office->sort_order,
                ],
                'tg' => [
                    'region' => $office->getTranslation('region', 'tg', false),
                    'address' => $office->getTranslation('address', 'tg', false),
                    'hours' => $office->getTranslation('hours', 'tg', false),
                ],
                'ru' => [
                    'region' => $office->getTranslation('region', 'ru', false),
                    'address' => $office->getTranslation('address', 'ru', false),
                    'hours' => $office->getTranslation('hours', 'ru', false),
                ],
                'en' => [
                    'region' => $office->getTranslation('region', 'en', false),
                    'address' => $office->getTranslation('address', 'en', false),
                    'hours' => $office->getTranslation('hours', 'en', false),
                ],
            ];

            $collection->entries()->create([
                'blueprint_id' => $blueprint->id,
                'status' => 'published',
                'data' => $data,
                'created_at' => $office->created_at,
                'updated_at' => $office->updated_at,
            ]);
        }
    }

    private function migrateRegions()
    {
        $collection = Collection::where('slug', 'regions')->firstOrFail();
        $blueprint = $collection->blueprints()->firstOrFail();
        $regions = Region::all();

        $this->info("Migrating {$regions->count()} Regions...");

        foreach ($regions as $region) {
            $data = [
                'global' => [
                    'slug' => $region->slug,
                    'risk' => $region->risk?->value ?? 'low',
                    'active_incidents' => $region->active_incidents,
                    'stations' => $region->stations,
                    'sort_order' => $region->sort_order,
                ],
                'tg' => [
                    'name' => $region->getTranslation('name', 'tg', false),
                    'center' => $region->getTranslation('center', 'tg', false),
                    'note' => $region->getTranslation('note', 'tg', false),
                ],
                'ru' => [
                    'name' => $region->getTranslation('name', 'ru', false),
                    'center' => $region->getTranslation('center', 'ru', false),
                    'note' => $region->getTranslation('note', 'ru', false),
                ],
                'en' => [
                    'name' => $region->getTranslation('name', 'en', false),
                    'center' => $region->getTranslation('center', 'en', false),
                    'note' => $region->getTranslation('note', 'en', false),
                ],
            ];

            $collection->entries()->create([
                'blueprint_id' => $blueprint->id,
                'status' => 'published',
                'data' => $data,
                'created_at' => $region->created_at,
                'updated_at' => $region->updated_at,
            ]);
        }
    }
}
