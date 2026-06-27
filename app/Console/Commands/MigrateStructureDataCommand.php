<?php

namespace App\Console\Commands;

use App\Core\Models\Collection;
use App\Core\Models\Entry;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateStructureDataCommand extends Command
{
    protected $signature = 'cms:migrate-structure';

    protected $description = 'Migrate legacy leaders, departments, directions, and programs to the dynamic entries table.';

    public function handle()
    {
        $this->info('Starting structure data migration...');

        $this->migrateLeaders();
        $this->migrateDepartments();
        $this->migrateDirections();
        $this->migratePrograms();

        $this->info('Migration complete!');
    }

    private function migrateLeaders()
    {
        $collection = Collection::where('slug', 'leaders')->first();
        if (! $collection) {
            $this->error('Leaders collection not found!');

            return;
        }
        $blueprint = $collection->blueprints()->first();

        $leaders = DB::table('leaders')->get();
        $count = 0;

        foreach ($leaders as $leader) {
            $name = json_decode($leader->name, true) ?? [];
            $role = json_decode($leader->role, true) ?? [];
            $rank = json_decode($leader->rank, true) ?? [];
            $bio = json_decode($leader->bio, true) ?? [];

            Entry::create([
                'collection_id' => $collection->id,
                'blueprint_id' => $blueprint->id,
                'author_id' => 1,
                'status' => 'published',
                'published_at' => now(),
                'data' => [
                    'tg' => [
                        'name' => $name['tg'] ?? '',
                        'role' => $role['tg'] ?? '',
                        'rank' => $rank['tg'] ?? '',
                        'bio' => $bio['tg'] ?? '',
                        'sort_order' => $leader->sort_order,
                    ],
                    'ru' => [
                        'name' => $name['ru'] ?? '',
                        'role' => $role['ru'] ?? '',
                        'rank' => $rank['ru'] ?? '',
                        'bio' => $bio['ru'] ?? '',
                        'sort_order' => $leader->sort_order,
                    ],
                    'en' => [
                        'name' => $name['en'] ?? '',
                        'role' => $role['en'] ?? '',
                        'rank' => $rank['en'] ?? '',
                        'bio' => $bio['en'] ?? '',
                        'sort_order' => $leader->sort_order,
                    ],
                ],
            ]);
            $count++;
        }
        $this->info("Migrated {$count} leaders.");
    }

    private function migrateDepartments()
    {
        $collection = Collection::where('slug', 'departments')->first();
        if (! $collection) {
            $this->error('Departments collection not found!');

            return;
        }
        $blueprint = $collection->blueprints()->first();

        $departments = DB::table('departments')->get();
        $count = 0;

        foreach ($departments as $department) {
            $title = json_decode($department->title, true) ?? [];
            $description = json_decode($department->description, true) ?? [];
            $head = json_decode($department->head, true) ?? [];

            Entry::create([
                'collection_id' => $collection->id,
                'blueprint_id' => $blueprint->id,
                'author_id' => 1,
                'status' => 'published',
                'published_at' => now(),
                'data' => [
                    'tg' => [
                        'icon' => $department->icon,
                        'title' => $title['tg'] ?? '',
                        'description' => $description['tg'] ?? '',
                        'head' => $head['tg'] ?? '',
                        'sort_order' => $department->sort_order,
                    ],
                    'ru' => [
                        'icon' => $department->icon,
                        'title' => $title['ru'] ?? '',
                        'description' => $description['ru'] ?? '',
                        'head' => $head['ru'] ?? '',
                        'sort_order' => $department->sort_order,
                    ],
                    'en' => [
                        'icon' => $department->icon,
                        'title' => $title['en'] ?? '',
                        'description' => $description['en'] ?? '',
                        'head' => $head['en'] ?? '',
                        'sort_order' => $department->sort_order,
                    ],
                ],
            ]);
            $count++;
        }
        $this->info("Migrated {$count} departments.");
    }

    private function migrateDirections()
    {
        $collection = Collection::where('slug', 'directions')->first();
        if (! $collection) {
            $this->error('Directions collection not found!');

            return;
        }
        $blueprint = $collection->blueprints()->first();

        $directions = DB::table('directions')->get();
        $count = 0;

        foreach ($directions as $direction) {
            $title = json_decode($direction->title, true) ?? [];
            $description = json_decode($direction->description, true) ?? [];
            $statLabel = json_decode($direction->stat_label, true) ?? [];

            Entry::create([
                'collection_id' => $collection->id,
                'blueprint_id' => $blueprint->id,
                'author_id' => 1,
                'status' => 'published',
                'published_at' => now(),
                'data' => [
                    'tg' => [
                        'key' => $direction->key,
                        'icon' => $direction->icon,
                        'stat_value' => $direction->stat_value,
                        'title' => $title['tg'] ?? '',
                        'description' => $description['tg'] ?? '',
                        'stat_label' => $statLabel['tg'] ?? '',
                        'sort_order' => $direction->sort_order,
                    ],
                    'ru' => [
                        'key' => $direction->key,
                        'icon' => $direction->icon,
                        'stat_value' => $direction->stat_value,
                        'title' => $title['ru'] ?? '',
                        'description' => $description['ru'] ?? '',
                        'stat_label' => $statLabel['ru'] ?? '',
                        'sort_order' => $direction->sort_order,
                    ],
                    'en' => [
                        'key' => $direction->key,
                        'icon' => $direction->icon,
                        'stat_value' => $direction->stat_value,
                        'title' => $title['en'] ?? '',
                        'description' => $description['en'] ?? '',
                        'stat_label' => $statLabel['en'] ?? '',
                        'sort_order' => $direction->sort_order,
                    ],
                ],
            ]);
            $count++;
        }
        $this->info("Migrated {$count} directions.");
    }

    private function migratePrograms()
    {
        $collection = Collection::where('slug', 'programs')->first();
        if (! $collection) {
            $this->error('Programs collection not found!');

            return;
        }
        $blueprint = $collection->blueprints()->first();

        $programs = DB::table('programs')->get();
        $count = 0;

        foreach ($programs as $program) {
            $title = json_decode($program->title, true) ?? [];
            $description = json_decode($program->description, true) ?? [];

            Entry::create([
                'collection_id' => $collection->id,
                'blueprint_id' => $blueprint->id,
                'author_id' => 1,
                'status' => 'published',
                'published_at' => now(),
                'data' => [
                    'tg' => [
                        'period' => $program->period,
                        'status' => $program->status,
                        'title' => $title['tg'] ?? '',
                        'description' => $description['tg'] ?? '',
                        'sort_order' => $program->sort_order,
                    ],
                    'ru' => [
                        'period' => $program->period,
                        'status' => $program->status,
                        'title' => $title['ru'] ?? '',
                        'description' => $description['ru'] ?? '',
                        'sort_order' => $program->sort_order,
                    ],
                    'en' => [
                        'period' => $program->period,
                        'status' => $program->status,
                        'title' => $title['en'] ?? '',
                        'description' => $description['en'] ?? '',
                        'sort_order' => $program->sort_order,
                    ],
                ],
            ]);
            $count++;
        }
        $this->info("Migrated {$count} programs.");
    }
}
