<?php

namespace Database\Seeders;

use App\Enums\ProgramStatus;
use App\Models\Direction;
use App\Models\Program;
use Illuminate\Database\Seeder;

class ActivitiesSeeder extends Seeder
{
    public function run(): void
    {
        /** @var list<array{id:string,icon:string,title:string,description:string,value:string,label:string}> $directions */
        $directions = [
            [
                'id' => 'rescue',
                'icon' => 'LifeBuoy',
                'title' => 'Корҳои ҷустуҷӯию наҷотдиҳӣ',
                'description' => 'Поиск и спасение людей при стихийных бедствиях, авариях и происшествиях в горной и водной среде.',
                'value' => '12 480',
                'label' => 'спасено за год',
            ],
            [
                'id' => 'prevention',
                'icon' => 'ShieldAlert',
                'title' => 'Пешгирии ҳолатҳои фавқулодда',
                'description' => 'Мониторинг рисков, прогнозирование и предупреждение чрезвычайных ситуаций природного характера.',
                'value' => '320+',
                'label' => 'пунктов мониторинга',
            ],
            [
                'id' => 'civil-defense',
                'icon' => 'Users',
                'title' => 'Мудофиаи гражданӣ',
                'description' => 'Защита населения и территорий, организация эвакуации и пунктов временного размещения.',
                'value' => '1 200',
                'label' => 'учений в год',
            ],
            [
                'id' => 'fire',
                'icon' => 'Flame',
                'title' => 'Бехатарии оташнишонӣ',
                'description' => 'Тушение пожаров, надзор за соблюдением требований пожарной безопасности на объектах.',
                'value' => '8 мин',
                'label' => 'среднее время реакции',
            ],
            [
                'id' => 'hydromet',
                'icon' => 'CloudRain',
                'title' => 'Гидрометеорология',
                'description' => 'Наблюдение за погодой, состоянием рек, ледников и горных озёр, раннее оповещение.',
                'value' => '24/7',
                'label' => 'режим наблюдения',
            ],
            [
                'id' => 'training',
                'icon' => 'GraduationCap',
                'title' => 'Омӯзиши аҳолӣ',
                'description' => 'Обучение населения и специалистов правилам поведения и действиям при чрезвычайных ситуациях.',
                'value' => '45 000',
                'label' => 'обучено за год',
            ],
        ];

        $order = 0;
        foreach ($directions as $item) {
            Direction::updateOrCreate(
                ['key' => $item['id']],
                [
                    'icon' => $item['icon'],
                    'title' => ['tg' => $item['title'], 'ru' => $item['title'], 'en' => $item['title']],
                    'description' => ['tg' => $item['description'], 'ru' => $item['description'], 'en' => $item['description']],
                    'stat_value' => $item['value'],
                    'stat_label' => ['tg' => $item['label'], 'ru' => $item['label'], 'en' => $item['label']],
                    'sort_order' => $order++,
                ],
            );
        }

        /** @var list<array{title:string,period:string,status:string,description:string}> $programs */
        $programs = [
            [
                'title' => 'Стратегияи миллии рушд то соли 2030',
                'period' => '2016–2030',
                'status' => 'Амалкунанда',
                'description' => 'Снижение риска бедствий и повышение устойчивости общин к стихийным явлениям.',
            ],
            [
                'title' => 'Барномаи давлатии огоҳии барвақт',
                'period' => '2023–2027',
                'status' => 'Амалкунанда',
                'description' => 'Развитие национальной системы раннего оповещения о чрезвычайных ситуациях.',
            ],
            [
                'title' => 'Мактаби бехатар',
                'period' => '2024–2028',
                'status' => 'Амалкунанда',
                'description' => 'Обучение школьников и педагогов действиям при землетрясениях и пожарах.',
            ],
            [
                'title' => 'Навсозии техникаи наҷотдиҳӣ',
                'period' => '2025–2026',
                'status' => 'Ба нақша',
                'description' => 'Модернизация парка аварийно-спасательной техники и средств связи.',
            ],
        ];

        $order = 0;
        foreach ($programs as $item) {
            Program::updateOrCreate(
                ['title->tg' => $item['title']],
                [
                    'title' => ['tg' => $item['title'], 'ru' => $item['title'], 'en' => $item['title']],
                    'description' => ['tg' => $item['description'], 'ru' => $item['description'], 'en' => $item['description']],
                    'period' => $item['period'],
                    'status' => ProgramStatus::from($item['status']),
                    'sort_order' => $order++,
                ],
            );
        }
    }
}
