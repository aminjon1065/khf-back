<?php

declare(strict_types=1);

namespace Database\Factories\Modules\Navigation;

use App\Modules\Navigation\Enums\NavigationSourceType;
use App\Modules\Navigation\Enums\NavigationVisibility;
use App\Modules\Navigation\Models\Navigation;
use App\Modules\Navigation\Models\NavigationItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NavigationItem>
 */
final class NavigationItemFactory extends Factory
{
    protected $model = NavigationItem::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $label = fake()->words(2, true);

        return [
            'navigation_id' => Navigation::factory(),
            'parent_id' => null,
            'order' => 0,
            'label' => ['tg' => $label, 'ru' => $label, 'en' => $label],
            'source_type' => NavigationSourceType::ExternalUrl,
            'source_id' => null,
            'source_value' => fake()->url(),
            'target' => '_self',
            'visibility' => NavigationVisibility::Public,
            'visibility_rules' => null,
            'generator' => null,
            'meta' => null,
            'is_active' => true,
        ];
    }

    public function forNavigation(Navigation $navigation): self
    {
        return $this->state(fn (): array => ['navigation_id' => $navigation->id]);
    }

    public function childOf(NavigationItem $parent): self
    {
        return $this->state(fn (): array => [
            'navigation_id' => $parent->navigation_id,
            'parent_id' => $parent->id,
        ]);
    }

    public function ordered(int $order): self
    {
        return $this->state(fn (): array => ['order' => $order]);
    }

    /**
     * @param  list<string>  $rules
     */
    public function visibility(NavigationVisibility $visibility, array $rules = []): self
    {
        return $this->state(fn (): array => [
            'visibility' => $visibility,
            'visibility_rules' => $rules === [] ? null : $rules,
        ]);
    }
}
