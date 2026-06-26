<?php

namespace Database\Seeders;

use App\Models\Hotline;
use App\Models\Office;
use Illuminate\Database\Seeder;

class ContactsSeeder extends Seeder
{
    public function run(): void
    {
        /** @var list<array{number:string,label:string,note:string,primary:bool}> $hotlines */
        $hotlines = [
            [
                'number' => '112',
                'label' => 'Хадамоти ягонаи наҷот',
                'note' => 'Круглосуточно, бесплатно со всех операторов',
                'primary' => true,
            ],
            [
                'number' => '(992 37) 221-12-12',
                'label' => 'Дежурно-диспетчерская служба',
                'note' => 'Оперативный дежурный Комитета',
                'primary' => false,
            ],
            [
                'number' => '(992 37) 223-13-11',
                'label' => 'Хатти боварӣ',
                'note' => 'Телефон доверия, приём обращений граждан',
                'primary' => false,
            ],
            [
                'number' => '(992 37) 224-18-00',
                'label' => 'Пресс-центр',
                'note' => 'Для СМИ и запросов о деятельности Комитета',
                'primary' => false,
            ],
        ];

        $order = 0;
        foreach ($hotlines as $hotline) {
            Hotline::updateOrCreate(
                ['number' => $hotline['number']],
                [
                    'label' => ['tg' => $hotline['label'], 'ru' => $hotline['label'], 'en' => $hotline['label']],
                    'note' => ['tg' => $hotline['note'], 'ru' => $hotline['note'], 'en' => $hotline['note']],
                    'is_primary' => $hotline['primary'],
                    'sort_order' => $order++,
                ],
            );
        }

        $headOffice = [
            'region' => 'Дастгоҳи марказӣ',
            'address' => '734013, ш. Душанбе, кӯчаи Лоҳутӣ 26',
            'phone' => '(992 37) 223-13-11',
            'email' => 'info@khf.tj',
            'hours' => 'Душанбе–Ҷумъа, 8:00–17:00',
        ];

        Office::updateOrCreate(
            ['email' => $headOffice['email']],
            [
                'region' => ['tg' => $headOffice['region'], 'ru' => $headOffice['region'], 'en' => $headOffice['region']],
                'address' => ['tg' => $headOffice['address'], 'ru' => $headOffice['address'], 'en' => $headOffice['address']],
                'phone' => $headOffice['phone'],
                'hours' => ['tg' => $headOffice['hours'], 'ru' => $headOffice['hours'], 'en' => $headOffice['hours']],
                'is_head' => true,
                'sort_order' => 0,
            ],
        );

        /** @var list<array{region:string,address:string,phone:string,email:string,hours:string}> $offices */
        $offices = [
            [
                'region' => 'вилояти Хатлон',
                'address' => 'ш. Бохтар, кӯчаи Истиқлол 14',
                'phone' => '(992 3222) 2-22-12',
                'email' => 'khatlon@khf.tj',
                'hours' => 'Душанбе–Ҷумъа, 8:00–17:00',
            ],
            [
                'region' => 'вилояти Суғд',
                'address' => 'ш. Хуҷанд, кӯчаи Ленин 180',
                'phone' => '(992 3422) 6-33-12',
                'email' => 'sugd@khf.tj',
                'hours' => 'Душанбе–Ҷумъа, 8:00–17:00',
            ],
            [
                'region' => 'ВМКБ',
                'address' => 'ш. Хоруғ, кӯчаи Ленин 1',
                'phone' => '(992 3522) 2-12-12',
                'email' => 'gbao@khf.tj',
                'hours' => 'Душанбе–Ҷумъа, 8:00–17:00',
            ],
            [
                'region' => 'ноҳияҳои тобеи ҷумҳурӣ',
                'address' => 'ш. Ваҳдат, кӯчаи Марказӣ 5',
                'phone' => '(992 37) 224-15-15',
                'email' => 'ntj@khf.tj',
                'hours' => 'Душанбе–Ҷумъа, 8:00–17:00',
            ],
        ];

        $order = 0;
        foreach ($offices as $office) {
            Office::updateOrCreate(
                ['email' => $office['email']],
                [
                    'region' => ['tg' => $office['region'], 'ru' => $office['region'], 'en' => $office['region']],
                    'address' => ['tg' => $office['address'], 'ru' => $office['address'], 'en' => $office['address']],
                    'phone' => $office['phone'],
                    'hours' => ['tg' => $office['hours'], 'ru' => $office['hours'], 'en' => $office['hours']],
                    'is_head' => false,
                    'sort_order' => $order++,
                ],
            );
        }
    }
}
