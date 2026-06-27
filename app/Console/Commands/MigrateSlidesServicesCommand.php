<?php

namespace App\Console\Commands;

use App\Core\Models\Collection;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateSlidesServicesCommand extends Command
{
    protected $signature = 'cms:migrate-slides-services';

    protected $description = 'Migrate legacy slides and services to entries';

    public function handle()
    {
        $this->info('Migrating Slides...');
        $slideCollection = Collection::where('slug', 'slides')->firstOrFail();

        $slides = DB::table('slides')->get();
        foreach ($slides as $slide) {
            $data = [
                'global' => [
                    'link' => $slide->link,
                    'sort_order' => $slide->sort_order,
                    'is_active' => $slide->is_active,
                ],
            ];

            foreach (['tg', 'ru', 'en'] as $locale) {
                $title = json_decode($slide->title, true)[$locale] ?? '';
                $subtitle = json_decode($slide->subtitle, true)[$locale] ?? '';
                $button_text = json_decode($slide->button_text, true)[$locale] ?? '';

                if ($title || $subtitle || $button_text) {
                    $data[$locale] = [
                        'title' => $title,
                        'subtitle' => $subtitle,
                        'button_text' => $button_text,
                    ];
                }
            }

            $entry = $slideCollection->entries()->create([
                'blueprint_id' => $slideCollection->blueprints()->first()->id,
                'status' => 'published',
                'data' => $data,
                'created_at' => $slide->created_at,
                'updated_at' => $slide->updated_at,
            ]);

        }

        $this->info('Migrating Services...');
        $serviceCollection = Collection::where('slug', 'services')->firstOrFail();

        $services = DB::table('services')->get();
        foreach ($services as $service) {
            $data = [
                'global' => [
                    'key' => $service->key,
                    'icon' => $service->icon,
                    'is_primary' => $service->is_primary,
                    'tel' => $service->tel,
                    'route_key' => $service->route_key,
                    'sort_order' => $service->sort_order,
                ],
            ];

            foreach (['tg', 'ru', 'en'] as $locale) {
                $title = json_decode($service->title, true)[$locale] ?? '';
                $subtitle = json_decode($service->subtitle, true)[$locale] ?? '';

                if ($title || $subtitle) {
                    $data[$locale] = [
                        'title' => $title,
                        'subtitle' => $subtitle,
                    ];
                }
            }

            $serviceCollection->entries()->create([
                'blueprint_id' => $serviceCollection->blueprints()->first()->id,
                'status' => 'published',
                'data' => $data,
                'created_at' => $service->created_at,
                'updated_at' => $service->updated_at,
            ]);
        }

        $this->info('Slides and Services migration completed!');
    }
}
