<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\Service;
use App\Models\Slide;
use Illuminate\Database\Seeder;

class HomeSeeder extends Seeder
{
    public function run(): void
    {
        /** @var list<array{news_slug:?string,category:array<string,string>,title:array<string,string>,date:string,source:string}> $slides */
        $slides = [
            [
                'news_slug' => 'spasateli-vyzvolili-troih-grazhdan-iz-reki-vahsh',
                'category' => ['tg' => 'Наҷот', 'ru' => 'Спасение', 'en' => 'Rescue'],
                'title' => [
                    'tg' => 'Наҷотдиҳандагон се нафарро аз дарёи Вахш раҳоӣ доданд',
                    'ru' => 'Спасатели вызволили троих граждан из реки Вахш в Хатлонской области',
                    'en' => 'Rescuers pulled three people from the Vakhsh River in Khatlon',
                ],
                'date' => '14.06.2026',
                'source' => 'Пресс-центр КҲФ',
            ],
            [
                'news_slug' => 'mezhdunarodnoe-vzaimodejstvie-komiteta',
                'category' => ['tg' => 'Ҳамкорӣ', 'ru' => 'Сотрудничество', 'en' => 'Cooperation'],
                'title' => [
                    'tg' => 'Ҳамкории байналмилалии Кумита густариш меёбад',
                    'ru' => 'Международное взаимодействие Комитета продолжает расширяться',
                    'en' => 'The Committee’s international cooperation keeps expanding',
                ],
                'date' => '13.06.2026',
                'source' => 'Пресс-центр КҲФ',
            ],
            [
                'news_slug' => 'proverka-podgotovki-lichnogo-sostava-vmkb',
                'category' => ['tg' => 'ВМКБ', 'ru' => 'ВМКБ', 'en' => 'GBAO'],
                'title' => [
                    'tg' => 'Санҷиши омодагии касбии шахсии ҳайат',
                    'ru' => 'Проверка профессиональной подготовки личного состава',
                    'en' => 'Inspection of personnel professional readiness',
                ],
                'date' => '12.06.2026',
                'source' => 'Пресс-центр КҲФ',
            ],
        ];

        $order = 0;
        foreach ($slides as $slide) {
            $newsId = $slide['news_slug'] !== null
                ? News::query()->where('slug', $slide['news_slug'])->value('id')
                : null;

            Slide::updateOrCreate(
                ['title->ru' => $slide['title']['ru']],
                [
                    'news_id' => $newsId,
                    'category' => $slide['category'],
                    'title' => $slide['title'],
                    'date' => $slide['date'],
                    'source' => $slide['source'],
                    'sort_order' => $order++,
                ],
            );
        }

        /** @var list<array{key:string,icon:string,title:array<string,string>,subtitle:array<string,string>,is_primary:bool,tel:?string,route_key:?string}> $services */
        $services = [
            [
                'key' => '112',
                'icon' => 'Phone',
                'title' => ['tg' => '112', 'ru' => '112', 'en' => '112'],
                'subtitle' => ['tg' => 'Тамоси фавқулодда', 'ru' => 'Экстренный вызов', 'en' => 'Emergency call'],
                'is_primary' => true,
                'tel' => '112',
                'route_key' => null,
            ],
            [
                'key' => 'report',
                'icon' => 'Send',
                'title' => ['tg' => 'Хабар додан дар бораи ҲФ', 'ru' => 'Сообщить о ЧС', 'en' => 'Report an emergency'],
                'subtitle' => ['tg' => 'Дархости онлайн', 'ru' => 'Онлайн-заявка', 'en' => 'Online request'],
                'is_primary' => false,
                'tel' => null,
                'route_key' => 'report',
            ],
            [
                'key' => 'howto',
                'icon' => 'BookOpen',
                'title' => ['tg' => 'Ҳангоми ҲФ чӣ бояд кард', 'ru' => 'Что делать при ЧС', 'en' => 'What to do in an emergency'],
                'subtitle' => ['tg' => 'Дастурҳо ва тавсияҳо', 'ru' => 'Памятки и инструкции', 'en' => 'Guides and instructions'],
                'is_primary' => false,
                'tel' => null,
                'route_key' => 'safety',
            ],
            [
                'key' => 'subscribe',
                'icon' => 'Bell',
                'title' => ['tg' => 'Обуна', 'ru' => 'Подписка', 'en' => 'Subscription'],
                'subtitle' => ['tg' => 'Огоҳиҳо аз рӯи минтақа', 'ru' => 'Оповещения по региону', 'en' => 'Regional alerts'],
                'is_primary' => false,
                'tel' => null,
                'route_key' => 'subscribe',
            ],
        ];

        $order = 0;
        foreach ($services as $service) {
            Service::updateOrCreate(
                ['key' => $service['key']],
                [
                    'icon' => $service['icon'],
                    'title' => $service['title'],
                    'subtitle' => $service['subtitle'],
                    'is_primary' => $service['is_primary'],
                    'tel' => $service['tel'],
                    'route_key' => $service['route_key'],
                    'sort_order' => $order++,
                ],
            );
        }
    }
}
