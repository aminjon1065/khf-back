<?php

use App\Models\Department;
use App\Models\Direction;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\ForumCategory;
use App\Models\ForumTopic;
use App\Models\Hotline;
use App\Models\Leader;
use App\Models\News;
use App\Models\Office;
use App\Models\Program;
use App\Models\Region;
use App\Models\RegionalOffice;
use App\Models\Service;
use App\Models\Slide;
use App\Models\User;
use Database\Seeders\ActivitiesSeeder;
use Database\Seeders\ContactsSeeder;
use Database\Seeders\DocumentSeeder;
use Database\Seeders\ForumSeeder;
use Database\Seeders\HomeSeeder;
use Database\Seeders\NewsSeeder;
use Database\Seeders\RegionSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Database\Seeders\SettingsSeeder;
use Database\Seeders\StructureSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    Http::fake();
    $this->seed([
        RolesAndPermissionsSeeder::class,
        SettingsSeeder::class,
        NewsSeeder::class,
        DocumentSeeder::class,
        StructureSeeder::class,
        ActivitiesSeeder::class,
        ForumSeeder::class,
        RegionSeeder::class,
        ContactsSeeder::class,
        HomeSeeder::class,
    ]);
});

function adminUserContent(): User
{
    return User::where('email', 'admin@khf.tj')->firstOrFail();
}

/**
 * @return array<string, class-string<\Illuminate\Database\Eloquent\Model>>
 */
function collectionModels(): array
{
    return [
        'news' => News::class,
        'documents' => Document::class,
        'document-categories' => DocumentCategory::class,
        'leaders' => Leader::class,
        'departments' => Department::class,
        'regional-offices' => RegionalOffice::class,
        'directions' => Direction::class,
        'programs' => Program::class,
        'forum-categories' => ForumCategory::class,
        'forum-topics' => ForumTopic::class,
        'regions' => Region::class,
        'hotlines' => Hotline::class,
        'offices' => Office::class,
        'slides' => Slide::class,
        'services' => Service::class,
    ];
}

it('opens every collection index in the admin', function (): void {
    foreach (array_keys(collectionModels()) as $path) {
        $this->actingAs(adminUserContent())->get("/admin/{$path}")->assertOk();
    }
});

it('opens every collection edit page (route-model binding)', function (): void {
    foreach (collectionModels() as $path => $model) {
        $id = $model::query()->value('id');
        expect($id)->not->toBeNull();
        $this->actingAs(adminUserContent())->get("/admin/{$path}/{$id}/edit")->assertOk();
    }
});

it('creates a region from the admin', function (): void {
    $this->actingAs(adminUserContent())->post('/admin/regions', [
        'name' => ['ru' => 'Тестовый регион', 'tg' => 'Минтақаи озмоишӣ', 'en' => 'Test region'],
        'center' => ['ru' => 'Центр', 'tg' => '', 'en' => ''],
        'note' => ['ru' => '', 'tg' => '', 'en' => ''],
        'slug' => 'test-region',
        'risk' => 'low',
        'active_incidents' => 0,
        'stations' => 0,
        'sort_order' => 0,
    ])->assertRedirect('/admin/regions');

    $this->assertDatabaseHas('regions', ['slug' => 'test-region']);
});
