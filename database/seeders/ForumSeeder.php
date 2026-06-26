<?php

namespace Database\Seeders;

use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    public function run(): void
    {
        /** @var list<array{id:string,title:string,description:string,topics:int,posts:int,icon:string}> $categories */
        $categories = [
            [
                'id' => 'general',
                'title' => 'Умумӣ',
                'description' => 'Общие вопросы безопасности и работы Комитета.',
                'topics' => 124,
                'posts' => 980,
                'icon' => 'MessagesSquare',
            ],
            [
                'id' => 'alerts',
                'title' => 'Огоҳӣ ва пешгирӣ',
                'description' => 'Раннее оповещение, прогнозы, обсуждение рисков в регионах.',
                'topics' => 86,
                'posts' => 612,
                'icon' => 'ShieldAlert',
            ],
            [
                'id' => 'help',
                'title' => 'Кӯмаки тарафайн',
                'description' => 'Взаимопомощь жителей при чрезвычайных ситуациях.',
                'topics' => 57,
                'posts' => 433,
                'icon' => 'HeartHandshake',
            ],
            [
                'id' => 'qa',
                'title' => 'Саволу ҷавоб',
                'description' => 'Вопросы специалистам Комитета и ответы на них.',
                'topics' => 203,
                'posts' => 1487,
                'icon' => 'HelpCircle',
            ],
        ];

        $order = 0;
        foreach ($categories as $category) {
            ForumCategory::updateOrCreate(
                ['slug' => $category['id']],
                [
                    'icon' => $category['icon'],
                    'title' => [
                        'tg' => $category['title'],
                        'ru' => $category['title'],
                        'en' => $category['title'],
                    ],
                    'description' => [
                        'tg' => $category['description'],
                        'ru' => $category['description'],
                        'en' => $category['description'],
                    ],
                    'topics_count' => $category['topics'],
                    'posts_count' => $category['posts'],
                    'sort_order' => $order++,
                ],
            );
        }

        /** @var list<array{id:string,title:string,category:string,author:string,replies:int,views:int,lastActivity:string,pinned:bool}> $topics */
        $topics = [
            [
                'id' => 't1',
                'title' => 'Правила поведения при сходе селя — собираем памятку',
                'category' => 'alerts',
                'author' => 'Дилшод_77',
                'replies' => 42,
                'views' => 1820,
                'lastActivity' => '2 соат пеш',
                'pinned' => true,
            ],
            [
                'id' => 't2',
                'title' => 'Как подписаться на оповещения по Хатлонской области?',
                'category' => 'qa',
                'author' => 'Гулнора',
                'replies' => 11,
                'views' => 540,
                'lastActivity' => '5 соат пеш',
                'pinned' => false,
            ],
            [
                'id' => 't3',
                'title' => 'Куда сообщать о трещинах на склоне у дороги?',
                'category' => 'help',
                'author' => 'Фаррух',
                'replies' => 8,
                'views' => 312,
                'lastActivity' => 'имрӯз, 09:14',
                'pinned' => false,
            ],
            [
                'id' => 't4',
                'title' => 'Сейсмостойкость домов — что важно знать жителям',
                'category' => 'general',
                'author' => 'инженер_Б',
                'replies' => 27,
                'views' => 1190,
                'lastActivity' => 'дирӯз',
                'pinned' => false,
            ],
            [
                'id' => 't5',
                'title' => 'Готовимся к паводковому сезону: чек-лист для семьи',
                'category' => 'alerts',
                'author' => 'Мадина',
                'replies' => 19,
                'views' => 760,
                'lastActivity' => 'дирӯз',
                'pinned' => false,
            ],
            [
                'id' => 't6',
                'title' => 'Где пройти курсы первой помощи в Душанбе?',
                'category' => 'qa',
                'author' => 'Шаҳзод',
                'replies' => 14,
                'views' => 489,
                'lastActivity' => '2 рӯз пеш',
                'pinned' => false,
            ],
        ];

        $order = 0;
        foreach ($topics as $topic) {
            $category = ForumCategory::query()->where('slug', $topic['category'])->first();

            ForumTopic::updateOrCreate(
                ['title->ru' => $topic['title']],
                [
                    'forum_category_id' => $category?->id,
                    'title' => [
                        'tg' => $topic['title'],
                        'ru' => $topic['title'],
                        'en' => $topic['title'],
                    ],
                    'author' => $topic['author'],
                    'replies' => $topic['replies'],
                    'views' => $topic['views'],
                    'pinned' => $topic['pinned'],
                    'last_activity' => $topic['lastActivity'],
                    'sort_order' => $order++,
                ],
            );
        }

        Setting::put('forum_stats', [
            'members' => '8 420',
            'topics' => '470',
            'posts' => '3 512',
            'online' => '63',
        ]);
    }
}
