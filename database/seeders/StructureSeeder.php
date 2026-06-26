<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Leader;
use App\Models\RegionalOffice;
use Illuminate\Database\Seeder;

class StructureSeeder extends Seeder
{
    public function run(): void
    {
        /** @var list<array{name:string,role:string,rank:string,bio:string}> $leaders */
        $leaders = [
            [
                'name' => 'Рустам Назарзода',
                'role' => 'Раиси Кумита',
                'rank' => 'генерал-лейтенант',
                'bio' => 'Осуществляет общее руководство деятельностью Комитета, координацию сил и средств при ликвидации чрезвычайных ситуаций.',
            ],
            [
                'name' => 'Далер Каримзода',
                'role' => 'Муовини якуми раис',
                'rank' => 'генерал-майор',
                'bio' => 'Курирует вопросы оперативного реагирования, поисково-спасательных работ и готовности подразделений.',
            ],
            [
                'name' => 'Фирӯза Саидова',
                'role' => 'Муовини раис',
                'rank' => 'полковник',
                'bio' => 'Отвечает за гражданскую оборону, обучение населения и международное сотрудничество.',
            ],
            [
                'name' => 'Шерали Ҳакимзода',
                'role' => 'Муовини раис',
                'rank' => 'полковник',
                'bio' => 'Курирует материально-техническое обеспечение, связь и информационные технологии.',
            ],
        ];

        $order = 0;
        foreach ($leaders as $leader) {
            Leader::updateOrCreate(
                ['name->ru' => $leader['name']],
                [
                    'name' => ['tg' => $leader['name'], 'ru' => $leader['name'], 'en' => $leader['name']],
                    'role' => ['tg' => $leader['role'], 'ru' => $leader['role'], 'en' => $leader['role']],
                    'rank' => ['tg' => $leader['rank'], 'ru' => $leader['rank'], 'en' => $leader['rank']],
                    'bio' => ['tg' => $leader['bio'], 'ru' => $leader['bio'], 'en' => $leader['bio']],
                    'sort_order' => $order++,
                ],
            );
        }

        /** @var list<array{title:string,description:string,head:string}> $departments */
        $departments = [
            [
                'title' => 'Сарраёсати амалиётӣ',
                'description' => 'Оперативное управление силами и средствами, дежурно-диспетчерская служба 112.',
                'head' => 'управление',
            ],
            [
                'title' => 'Раёсати корҳои ҷустуҷӯию наҷотдиҳӣ',
                'description' => 'Организация и проведение поисково-спасательных и аварийно-спасательных работ.',
                'head' => 'управление',
            ],
            [
                'title' => 'Раёсати мудофиаи гражданӣ',
                'description' => 'Планирование и проведение мероприятий гражданской обороны, защита населения.',
                'head' => 'управление',
            ],
            [
                'title' => 'Раёсати пешгирии ҲФ',
                'description' => 'Предупреждение чрезвычайных ситуаций, мониторинг и прогнозирование рисков.',
                'head' => 'управление',
            ],
            [
                'title' => 'Хадамоти давлатии оташнишонӣ',
                'description' => 'Обеспечение пожарной безопасности и тушение пожаров на территории республики.',
                'head' => 'служба',
            ],
            [
                'title' => 'Маркази таълимӣ',
                'description' => 'Подготовка спасателей, обучение населения и специалистов действиям при ЧС.',
                'head' => 'центр',
            ],
        ];

        $icons = ['Building2', 'Shield', 'Radio'];
        $order = 0;
        foreach ($departments as $i => $department) {
            Department::updateOrCreate(
                ['title->ru' => $department['title']],
                [
                    'icon' => $icons[$i % count($icons)],
                    'title' => ['tg' => $department['title'], 'ru' => $department['title'], 'en' => $department['title']],
                    'description' => ['tg' => $department['description'], 'ru' => $department['description'], 'en' => $department['description']],
                    'head' => ['tg' => $department['head'], 'ru' => $department['head'], 'en' => $department['head']],
                    'sort_order' => $order++,
                ],
            );
        }

        /** @var list<array{region:string,head:string,phone:string,address:string}> $offices */
        $offices = [
            [
                'region' => 'ш. Душанбе',
                'head' => 'полковник А. Раҳимов',
                'phone' => '(992 37) 221-12-12',
                'address' => 'ш. Душанбе, кӯчаи Лоҳутӣ 26',
            ],
            [
                'region' => 'вилояти Хатлон',
                'head' => 'полковник М. Сафаров',
                'phone' => '(992 3222) 2-22-12',
                'address' => 'ш. Бохтар, кӯчаи Истиқлол 14',
            ],
            [
                'region' => 'вилояти Суғд',
                'head' => 'полковник Б. Юсуфзода',
                'phone' => '(992 3422) 6-33-12',
                'address' => 'ш. Хуҷанд, кӯчаи Ленин 180',
            ],
            [
                'region' => 'ВМКБ',
                'head' => 'полковник Н. Давлатов',
                'phone' => '(992 3522) 2-12-12',
                'address' => 'ш. Хоруғ, кӯчаи Ленин 1',
            ],
            [
                'region' => 'ноҳияҳои тобеи ҷумҳурӣ',
                'head' => 'полковник Ҷ. Назаров',
                'phone' => '(992 37) 224-15-15',
                'address' => 'ш. Ваҳдат, кӯчаи Марказӣ 5',
            ],
        ];

        $order = 0;
        foreach ($offices as $office) {
            RegionalOffice::updateOrCreate(
                ['region->ru' => $office['region']],
                [
                    'region' => ['tg' => $office['region'], 'ru' => $office['region'], 'en' => $office['region']],
                    'head' => ['tg' => $office['head'], 'ru' => $office['head'], 'en' => $office['head']],
                    'phone' => $office['phone'],
                    'address' => ['tg' => $office['address'], 'ru' => $office['address'], 'en' => $office['address']],
                    'sort_order' => $order++,
                ],
            );
        }
    }
}
