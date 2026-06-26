<?php

namespace Database\Seeders;

use App\Enums\PublishStatus;
use App\Enums\Tone;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        /** @var array<string, array{Tone, array<string,string>}> $categories */
        $categories = [
            'spasenie' => [Tone::Alert, ['tg' => 'Наҷот', 'ru' => 'Спасение', 'en' => 'Rescue']],
            'sotrudnichestvo' => [Tone::Brand, ['tg' => 'Ҳамкорӣ', 'ru' => 'Сотрудничество', 'en' => 'Cooperation']],
            'vmkb' => [Tone::Success, ['tg' => 'ВМКБ', 'ru' => 'ВМКБ', 'en' => 'GBAO']],
            'profilaktika' => [Tone::Warn, ['tg' => 'Пешгирӣ', 'ru' => 'Профилактика', 'en' => 'Prevention']],
            'gidromet' => [Tone::Brand, ['tg' => 'Гидрометео', 'ru' => 'Гидрометеорология', 'en' => 'Hydrometeorology']],
            'pozhar' => [Tone::Alert, ['tg' => 'Бехатарии оташ', 'ru' => 'Пожарная безопасность', 'en' => 'Fire safety']],
            'obuchenie' => [Tone::Success, ['tg' => 'Омӯзиш', 'ru' => 'Обучение', 'en' => 'Training']],
            'tehnika' => [Tone::Brand, ['tg' => 'Техника', 'ru' => 'Техника', 'en' => 'Equipment']],
            'sel' => [Tone::Warn, ['tg' => 'Сел', 'ru' => 'Сель', 'en' => 'Mudflow']],
            'mezhdunarodnoe' => [Tone::Brand, ['tg' => 'Байналмилалӣ', 'ru' => 'Международное', 'en' => 'International']],
        ];

        $categoryModels = [];
        $order = 0;
        foreach ($categories as $slug => [$tone, $name]) {
            $categoryModels[$slug] = NewsCategory::updateOrCreate(
                ['slug' => $slug],
                ['name' => $name, 'tone' => $tone, 'sort_order' => $order++],
            );
        }

        /** @var list<array{slug:string,cat:string,title:array<string,string>,excerpt:string,body:list<string>,region:string,views:int,date:string}> $items */
        $items = [
            [
                'slug' => 'spasateli-vyzvolili-troih-grazhdan-iz-reki-vahsh',
                'cat' => 'spasenie',
                'title' => [
                    'tg' => 'Наҷотдиҳандагон се нафарро аз дарёи Вахш раҳоӣ доданд',
                    'ru' => 'Спасатели вызволили троих граждан из реки Вахш в Хатлонской области',
                    'en' => 'Rescuers pulled three people from the Vakhsh River in Khatlon',
                ],
                'excerpt' => 'Поисково-спасательное подразделение Комитета оперативно отреагировало на сообщение о людях, унесённых течением реки.',
                'body' => [
                    'Сообщение о трёх гражданах, оказавшихся в воде у населённого пункта на берегу реки Вахш, поступило на единый номер 112 утром. На место немедленно выехала дежурная поисково-спасательная группа.',
                    'С применением спасательных плавсредств все три человека были подняты на берег и переданы бригаде скорой помощи. Угрозы жизни нет.',
                ],
                'region' => 'Хатлон', 'views' => 1842, 'date' => '2026-06-14',
            ],
            [
                'slug' => 'mezhdunarodnoe-vzaimodejstvie-komiteta',
                'cat' => 'sotrudnichestvo',
                'title' => [
                    'tg' => 'Ҳамкории байналмилалии Кумита густариш меёбад',
                    'ru' => 'Международное взаимодействие Комитета продолжает расширяться',
                    'en' => 'The Committee’s international cooperation keeps expanding',
                ],
                'excerpt' => 'Подписан меморандум о сотрудничестве в области предупреждения и ликвидации последствий стихийных бедствий.',
                'body' => [
                    'В Душанбе состоялась рабочая встреча руководства Комитета с делегацией партнёрской службы по чрезвычайным ситуациям.',
                    'По итогам встречи подписан меморандум о совместных учениях, обмене данными мониторинга и подготовке специалистов.',
                ],
                'region' => 'Душанбе', 'views' => 964, 'date' => '2026-06-13',
            ],
            [
                'slug' => 'proverka-podgotovki-lichnogo-sostava-vmkb',
                'cat' => 'vmkb',
                'title' => [
                    'tg' => 'Санҷиши омодагии касбии шахсии ҳайат',
                    'ru' => 'Проверка профессиональной подготовки личного состава',
                    'en' => 'Inspection of personnel professional readiness',
                ],
                'excerpt' => 'В Горно-Бадахшанской автономной области прошли плановые проверки готовности подразделений.',
                'body' => [
                    'В подразделениях Комитета в ГБАО завершилась плановая проверка профессиональной и физической подготовки личного состава.',
                    'Спасатели отработали действия при сходе лавин и завалах на горных дорогах. Уровень готовности признан удовлетворительным.',
                ],
                'region' => 'ВМКБ', 'views' => 723, 'date' => '2026-06-12',
            ],
            [
                'slug' => 'ucheniya-po-grazhdanskoj-oborone-v-hatlone',
                'cat' => 'profilaktika',
                'title' => [
                    'tg' => 'Машқҳои мудофиаи гражданӣ дар Хатлон',
                    'ru' => 'Учения по гражданской обороне прошли в Хатлонской области',
                    'en' => 'Civil defense drills held in Khatlon region',
                ],
                'excerpt' => 'Отработаны действия населения и оперативных служб при условном землетрясении.',
                'body' => [
                    'В рамках месячника гражданской обороны в Хатлонской области прошли масштабные учения.',
                    'Отработаны эвакуация населения, развёртывание пунктов временного размещения и оказание первой помощи.',
                ],
                'region' => 'Хатлон', 'views' => 651, 'date' => '2026-06-12',
            ],
            [
                'slug' => 'shtormovoe-preduprezhdenie-v-gornyh-rajonah',
                'cat' => 'gidromet',
                'title' => [
                    'tg' => 'Огоҳии тӯфонӣ дар ноҳияҳои кӯҳӣ эълон шуд',
                    'ru' => 'Объявлено штормовое предупреждение в горных районах республики',
                    'en' => 'Storm warning issued for mountain districts',
                ],
                'excerpt' => 'Ожидаются сильные дожди, возможны сели и подъём уровня воды в реках.',
                'body' => [
                    'По данным агентства гидрометеорологии, в ближайшие двое суток ожидаются сильные осадки и усиление ветра.',
                    'Населению рекомендуется воздержаться от поездок в горную местность. Оперативные службы переведены в режим повышенной готовности.',
                ],
                'region' => 'НТҶ', 'views' => 1330, 'date' => '2026-06-11',
            ],
            [
                'slug' => 'likvidirovan-pozhar-na-sklade-v-dushanbe',
                'cat' => 'pozhar',
                'title' => [
                    'tg' => 'Сӯхтор дар анбори Душанбе хомӯш карда шуд',
                    'ru' => 'Ликвидирован пожар на складе в Душанбе, пострадавших нет',
                    'en' => 'Warehouse fire in Dushanbe extinguished, no injuries',
                ],
                'excerpt' => 'Огонь на площади около 200 кв. м удалось локализовать в течение получаса.',
                'body' => [
                    'Сообщение о возгорании складского помещения поступило вечером. К месту были направлены пожарные расчёты Комитета.',
                    'Огонь локализован и потушен, распространения на соседние здания не допущено. Пострадавших нет.',
                ],
                'region' => 'Душанбе', 'views' => 889, 'date' => '2026-06-10',
            ],
            [
                'slug' => 'shkolniki-proshli-kurs-po-dejstviyam-pri-chs',
                'cat' => 'obuchenie',
                'title' => [
                    'tg' => 'Беш аз 2000 хонанда курси рафтор ҳангоми ҲФ-ро гузаштанд',
                    'ru' => 'Более 2000 школьников прошли курс по действиям при ЧС',
                    'en' => 'Over 2,000 students completed an emergency-response course',
                ],
                'excerpt' => 'Программа «Безопасная школа» охватила учебные заведения столицы и пригородов.',
                'body' => [
                    'Завершился очередной этап программы «Безопасная школа» с практическими занятиями для учащихся.',
                    'Дети узнали, как действовать при землетрясении, пожаре и других чрезвычайных ситуациях.',
                ],
                'region' => 'Душанбе', 'views' => 542, 'date' => '2026-06-09',
            ],
            [
                'slug' => 'komitet-poluchil-novoe-oborudovanie',
                'cat' => 'tehnika',
                'title' => [
                    'tg' => 'Кумита партияи нави таҷҳизоти наҷотдиҳӣ гирифт',
                    'ru' => 'Комитет получил новую партию спасательного оборудования',
                    'en' => 'The Committee received a new batch of rescue equipment',
                ],
                'excerpt' => 'Подразделения оснащены современными гидравлическими инструментами и средствами связи.',
                'body' => [
                    'В рамках программы модернизации подразделения получили гидравлический аварийно-спасательный инструмент и средства связи.',
                    'Новая техника позволит сократить время реагирования и повысить эффективность работ.',
                ],
                'region' => 'Душанбе', 'views' => 477, 'date' => '2026-06-07',
            ],
            [
                'slug' => 'vosstanovleno-dvizhenie-na-trasse-dushanbe-horog',
                'cat' => 'sel',
                'title' => [
                    'tg' => 'Ҳаракат дар роҳи Душанбе–Хоруғ пас аз сел барқарор шуд',
                    'ru' => 'Восстановлено движение на трассе Душанбе–Хорог после схода селя',
                    'en' => 'Traffic restored on the Dushanbe–Khorog road after a mudflow',
                ],
                'excerpt' => 'Силами Комитета и дорожных служб расчищен участок, перекрытый селевым потоком.',
                'body' => [
                    'В результате обильных осадков на участке автодороги Душанбе–Хорог сошёл селевой поток, движение было перекрыто.',
                    'С применением тяжёлой техники участок расчищен, движение восстановлено в штатном режиме. Пострадавших нет.',
                ],
                'region' => 'НТҶ', 'views' => 1105, 'date' => '2026-06-05',
            ],
            [
                'slug' => 'delegaciya-khf-na-regionalnom-forume',
                'cat' => 'mezhdunarodnoe',
                'title' => [
                    'tg' => 'Ҳайати КҲФ дар форуми минтақавӣ ширкат варзид',
                    'ru' => 'Делегация КҲФ участвовала в региональном форуме по снижению риска бедствий',
                    'en' => 'CoES delegation took part in a regional disaster-risk forum',
                ],
                'excerpt' => 'Обсуждены вопросы раннего оповещения и трансграничного взаимодействия служб.',
                'body' => [
                    'Представители Комитета приняли участие в региональном форуме по снижению риска стихийных бедствий.',
                    'Таджикистан представил национальный опыт мониторинга ледников и горных озёр.',
                ],
                'region' => 'Душанбе', 'views' => 398, 'date' => '2026-06-03',
            ],
        ];

        foreach ($items as $item) {
            $bodyHtml = collect($item['body'])
                ->map(fn (string $p): string => '<p>'.$p.'</p>')
                ->implode("\n");

            News::updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'news_category_id' => $categoryModels[$item['cat']]->id,
                    'title' => $item['title'],
                    'excerpt' => ['tg' => $item['excerpt'], 'ru' => $item['excerpt'], 'en' => $item['excerpt']],
                    'body' => ['tg' => $bodyHtml, 'ru' => $bodyHtml, 'en' => $bodyHtml],
                    'author' => 'Пресс-центр КҲФ',
                    'region' => $item['region'],
                    'views' => $item['views'],
                    'status' => PublishStatus::Published,
                    'published_at' => $item['date'].' 09:00:00',
                ],
            );
        }
    }
}
