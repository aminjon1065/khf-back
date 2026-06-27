<?php

declare(strict_types=1);

use App\Models\User;
use App\Modules\Identity\Authorization\Permissions;
use App\Modules\Identity\Authorization\Roles;
use App\Modules\Identity\Contracts\IdentityServiceInterface;
use App\Modules\Navigation\Contracts\NavigationManagerInterface;
use App\Modules\Navigation\Enums\NavigationSourceType;
use App\Modules\Navigation\Enums\NavigationVisibility;
use App\Modules\Navigation\Models\Navigation;
use App\Modules\Navigation\Models\NavigationItem;
use Database\Seeders\IdentityAccessSeeder;

beforeEach(function () {
    $this->seed(IdentityAccessSeeder::class);
    $this->nav = app(NavigationManagerInterface::class);
    $this->identity = app(IdentityServiceInterface::class);
});

function visibleItem(Navigation $navigation, NavigationVisibility $visibility, array $rules = []): void
{
    NavigationItem::factory()
        ->forNavigation($navigation)
        ->visibility($visibility, $rules)
        ->create([
            'label' => ['tg' => 'x', 'ru' => 'x', 'en' => 'x'],
            'source_type' => NavigationSourceType::StaticUrl,
            'source_value' => '/x',
        ]);
}

it('shows public items to everyone, including guests', function () {
    $navigation = Navigation::factory()->create(['handle' => 'pub']);
    visibleItem($navigation, NavigationVisibility::Public);

    expect($this->nav->tree('pub', 'en', null)->items)->toHaveCount(1);
});

it('hides authenticated-only items from guests', function () {
    $navigation = Navigation::factory()->create(['handle' => 'auth']);
    visibleItem($navigation, NavigationVisibility::Authenticated);

    expect($this->nav->tree('auth', 'en', null)->items)->toHaveCount(0)
        ->and($this->nav->tree('auth', 'en', User::factory()->create())->items)->toHaveCount(1);
});

it('shows role-gated items only to users holding the role', function () {
    $navigation = Navigation::factory()->create(['handle' => 'role']);
    visibleItem($navigation, NavigationVisibility::Roles, [Roles::EDITOR]);

    $editor = User::factory()->create();
    $this->identity->assignRole($editor, Roles::EDITOR);
    $viewer = User::factory()->create();

    expect($this->nav->tree('role', 'en', $editor)->items)->toHaveCount(1)
        ->and($this->nav->tree('role', 'en', $viewer)->items)->toHaveCount(0);
});

it('shows permission-gated items to holders and to super admins via the gate wildcard', function () {
    $navigation = Navigation::factory()->create(['handle' => 'perm']);
    visibleItem($navigation, NavigationVisibility::Permissions, [Permissions::ENTRIES_VIEW]);

    $granted = User::factory()->create();
    $this->identity->grantPermission($granted, Permissions::ENTRIES_VIEW);

    $superAdmin = User::factory()->create();
    $this->identity->assignRole($superAdmin, Roles::SUPER_ADMIN);

    $nobody = User::factory()->create();

    expect($this->nav->tree('perm', 'en', $granted)->items)->toHaveCount(1)
        ->and($this->nav->tree('perm', 'en', $superAdmin)->items)->toHaveCount(1)
        ->and($this->nav->tree('perm', 'en', $nobody)->items)->toHaveCount(0);
});

it('drops the subtree of a hidden parent', function () {
    $navigation = Navigation::factory()->create(['handle' => 'subtree']);
    $parent = NavigationItem::factory()
        ->forNavigation($navigation)
        ->visibility(NavigationVisibility::Authenticated)
        ->create([
            'label' => ['tg' => 'p', 'ru' => 'p', 'en' => 'p'],
            'source_type' => NavigationSourceType::StaticUrl,
            'source_value' => '/p',
        ]);
    NavigationItem::factory()->childOf($parent)->create([
        'label' => ['tg' => 'c', 'ru' => 'c', 'en' => 'c'],
        'source_type' => NavigationSourceType::StaticUrl,
        'source_value' => '/c',
    ]);

    // Guest: parent hidden -> whole branch gone.
    expect($this->nav->tree('subtree', 'en', null)->items)->toHaveCount(0)
        // Authenticated: parent + child visible.
        ->and($this->nav->tree('subtree', 'en', User::factory()->create())->items[0]->children)->toHaveCount(1);
});
