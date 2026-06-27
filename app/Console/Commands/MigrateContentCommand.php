<?php

namespace App\Console\Commands;

use App\Core\Models\Collection;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateContentCommand extends Command
{
    protected $signature = 'cms:migrate-content';

    protected $description = 'Migrate News, Documents, and Forums to entries';

    public function handle()
    {
        $this->migrateNews();
        $this->migrateDocuments();
        $this->migrateForum();

        $this->info('Content migration completed!');
    }

    private function migrateNews()
    {
        $this->info('Migrating News Categories...');
        $catCollection = Collection::where('slug', 'news-categories')->firstOrFail();

        $catMap = []; // old_id => new_uuid
        $cats = DB::table('news_categories')->get();
        foreach ($cats as $cat) {
            $data = [
                'global' => [
                    'sort_order' => $cat->sort_order,
                ],
            ];

            foreach (['tg', 'ru', 'en'] as $locale) {
                $title = json_decode($cat->name, true)[$locale] ?? '';
                if ($title) {
                    $data[$locale] = ['title' => $title];
                }
            }

            $entry = $catCollection->entries()->create([
                'blueprint_id' => $catCollection->blueprints()->first()->id,
                'status' => 'published',
                'data' => $data,
                'created_at' => $cat->created_at,
                'updated_at' => $cat->updated_at,
            ]);
            $catMap[$cat->id] = $entry->id;
        }

        $this->info('Migrating News...');
        $newsCollection = Collection::where('slug', 'news')->firstOrFail();
        $news = DB::table('news')->get();

        foreach ($news as $item) {
            $data = [
                'global' => [
                    'category_id' => $catMap[$item->news_category_id] ?? null,
                    'author' => $item->author,
                    'region' => $item->region,
                    'views' => $item->views,
                    'status' => $item->status,
                    'published_at' => $item->published_at,
                ],
            ];

            foreach (['tg', 'ru', 'en'] as $locale) {
                $title = json_decode($item->title, true)[$locale] ?? '';
                $excerpt = json_decode($item->excerpt, true)[$locale] ?? '';
                $body = json_decode($item->body, true)[$locale] ?? '';

                if ($title || $excerpt || $body) {
                    $data[$locale] = [
                        'title' => $title,
                        'excerpt' => $excerpt,
                        'body' => $body,
                    ];
                }
            }

            $entry = $newsCollection->entries()->create([
                'blueprint_id' => $newsCollection->blueprints()->first()->id,
                'status' => 'published',
                'data' => $data,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ]);

        }
    }

    private function migrateDocuments()
    {
        $this->info('Migrating Document Categories...');
        $catCollection = Collection::where('slug', 'document-categories')->firstOrFail();

        $catMap = [];
        $cats = DB::table('document_categories')->get();
        foreach ($cats as $cat) {
            $data = ['global' => ['sort_order' => $cat->sort_order]];
            foreach (['tg', 'ru', 'en'] as $locale) {
                $title = json_decode($cat->name, true)[$locale] ?? '';
                if ($title) {
                    $data[$locale] = ['title' => $title];
                }
            }
            $entry = $catCollection->entries()->create([
                'blueprint_id' => $catCollection->blueprints()->first()->id,
                'status' => 'published',
                'data' => $data,
                'created_at' => $cat->created_at,
                'updated_at' => $cat->updated_at,
            ]);
            $catMap[$cat->id] = $entry->id;
        }

        $this->info('Migrating Documents...');
        $docCollection = Collection::where('slug', 'documents')->firstOrFail();
        $docs = DB::table('documents')->get();

        foreach ($docs as $item) {
            $data = [
                'global' => [
                    'category_id' => $catMap[$item->document_category_id] ?? null,
                    'number' => $item->number,
                    'document_date' => $item->document_date,
                    'type' => $item->type,
                    'size' => $item->size,
                    'sort_order' => $item->sort_order,
                ],
            ];
            foreach (['tg', 'ru', 'en'] as $locale) {
                $title = json_decode($item->title, true)[$locale] ?? '';
                if ($title) {
                    $data[$locale] = ['title' => $title];
                }
            }
            $entry = $docCollection->entries()->create([
                'blueprint_id' => $docCollection->blueprints()->first()->id,
                'status' => 'published',
                'data' => $data,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ]);

        }
    }

    private function migrateForum()
    {
        $this->info('Migrating Forum Categories...');
        $catCollection = Collection::where('slug', 'forum-categories')->firstOrFail();

        $catMap = [];
        $cats = DB::table('forum_categories')->get();
        foreach ($cats as $cat) {
            $data = [
                'global' => [
                    'icon' => $cat->icon,
                    'sort_order' => $cat->sort_order,
                ],
            ];
            foreach (['tg', 'ru', 'en'] as $locale) {
                $title = json_decode($cat->title, true)[$locale] ?? '';
                $description = json_decode($cat->description, true)[$locale] ?? '';
                if ($title || $description) {
                    $data[$locale] = ['title' => $title, 'description' => $description];
                }
            }
            $entry = $catCollection->entries()->create([
                'blueprint_id' => $catCollection->blueprints()->first()->id,
                'status' => 'published',
                'data' => $data,
                'created_at' => $cat->created_at,
                'updated_at' => $cat->updated_at,
            ]);
            $catMap[$cat->id] = $entry->id;
        }

        $this->info('Migrating Forum Topics...');
        $topicCollection = Collection::where('slug', 'forum-topics')->firstOrFail();
        $topics = DB::table('forum_topics')->get();

        foreach ($topics as $item) {
            $data = [
                'global' => [
                    'category_id' => $catMap[$item->forum_category_id] ?? null,
                    'author' => $item->author,
                    'replies' => $item->replies,
                    'views' => $item->views,
                    'pinned' => $item->pinned,
                    'last_activity' => $item->last_activity,
                    'sort_order' => $item->sort_order,
                ],
            ];
            foreach (['tg', 'ru', 'en'] as $locale) {
                $title = json_decode($item->title, true)[$locale] ?? '';
                if ($title) {
                    $data[$locale] = ['title' => $title];
                }
            }
            $topicCollection->entries()->create([
                'blueprint_id' => $topicCollection->blueprints()->first()->id,
                'status' => 'published',
                'data' => $data,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ]);
        }
    }
}
