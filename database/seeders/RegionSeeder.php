<?php

namespace Database\Seeders;

use App\Enums\RiskLevel;
use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        /** @var list<array{slug:string,name:string,center:string,risk:RiskLevel,activeIncidents:int,stations:int,note:string}> $items */
        $items = [
            [
                'slug' => 'dushanbe',
                'name' => 'ш. Душанбе',
                'center' => 'Душанбе',
                'risk' => RiskLevel::Low,
                'activeIncidents' => 1,
                'stations' => 6,
                'note' => 'Обстановка стабильная, подразделения в режиме повседневной готовности.',
            ],
            [
                'slug' => 'khatlon',
                'name' => 'вилояти Хатлон',
                'center' => 'Бохтар',
                'risk' => RiskLevel::High,
                'activeIncidents' => 4,
                'stations' => 14,
                'note' => 'Повышенный риск паводков и селей у горных рек, силы в готовности.',
            ],
            [
                'slug' => 'sugd',
                'name' => 'вилояти Суғд',
                'center' => 'Хуҷанд',
                'risk' => RiskLevel::Medium,
                'activeIncidents' => 2,
                'stations' => 12,
                'note' => 'Местами возможны подъёмы воды в реках, ведётся мониторинг.',
            ],
            [
                'slug' => 'gbao',
                'name' => 'ВМКБ',
                'center' => 'Хоруғ',
                'risk' => RiskLevel::Medium,
                'activeIncidents' => 2,
                'stations' => 9,
                'note' => 'Риск схода лавин и камнепадов на горных дорогах.',
            ],
            [
                'slug' => 'ntj',
                'name' => 'ноҳияҳои тобеи ҷумҳурӣ',
                'center' => 'Ваҳдат',
                'risk' => RiskLevel::High,
                'activeIncidents' => 3,
                'stations' => 11,
                'note' => 'Селевая опасность на трассе Душанбе–Хорог, движение под контролем.',
            ],
        ];

        $order = 0;
        foreach ($items as $item) {
            Region::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'name' => ['tg' => $item['name'], 'ru' => $item['name'], 'en' => $item['name']],
                    'center' => ['tg' => $item['center'], 'ru' => $item['center'], 'en' => $item['center']],
                    'risk' => $item['risk'],
                    'active_incidents' => $item['activeIncidents'],
                    'stations' => $item['stations'],
                    'note' => ['tg' => $item['note'], 'ru' => $item['note'], 'en' => $item['note']],
                    'sort_order' => $order++,
                ],
            );
        }
    }
}
