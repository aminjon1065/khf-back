<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        Setting::put('president', [
            'name' => 'Эмомалӣ Раҳмон',
            'role' => [
                'tg' => 'Президенти Ҷумҳурии Тоҷикистон',
                'ru' => 'Президент Республики Таджикистан',
                'en' => 'President of the Republic of Tajikistan',
            ],
            'quote' => [
                'tg' => '«Таъмини бехатарии ҳаёти инсон вазифаи муҳимтарини давлат аст».',
                'ru' => '«Обеспечение безопасности жизни человека — важнейшая задача государства».',
                'en' => '“Ensuring the safety of human life is the state’s most important task.”',
            ],
            'href' => 'https://president.tj',
        ]);

        Setting::put('site_stats', [
            'today' => '1 240',
            'month' => '38 902',
            'rescued' => '12 480',
            'reaction' => '8 мин',
        ]);

        Setting::put('forum_stats', [
            'members' => '8 420',
            'topics' => '470',
            'posts' => '3 512',
            'online' => '63',
        ]);

        Setting::put('map_stats', [
            'regions' => 5,
            'stations' => 52,
            'activeIncidents' => 12,
            'monitoring' => '320+',
        ]);
    }
}
