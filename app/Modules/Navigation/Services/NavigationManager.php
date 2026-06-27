<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Services;

use App\Core\Contracts\EventBusInterface;
use App\Core\Contracts\HookManagerInterface;
use App\Models\User;
use App\Modules\Navigation\Contracts\NavigationCacheInterface;
use App\Modules\Navigation\Contracts\NavigationGeneratorInterface;
use App\Modules\Navigation\Contracts\NavigationGeneratorRegistryInterface;
use App\Modules\Navigation\Contracts\NavigationManagerInterface;
use App\Modules\Navigation\Contracts\NavigationRepositoryInterface;
use App\Modules\Navigation\Contracts\NavigationSourceRegistryInterface;
use App\Modules\Navigation\Contracts\NavigationSourceResolverInterface;
use App\Modules\Navigation\Contracts\NavigationTypeRegistryInterface;
use App\Modules\Navigation\Contracts\NavigationVisibilityEvaluatorInterface;
use App\Modules\Navigation\DTOs\NavigationData;
use App\Modules\Navigation\DTOs\NavigationItemData;
use App\Modules\Navigation\DTOs\NavigationNode;
use App\Modules\Navigation\DTOs\NavigationTree;
use App\Modules\Navigation\DTOs\NavigationTypeDefinition;
use App\Modules\Navigation\Enums\NavigationVisibility;
use App\Modules\Navigation\Events\NavigationCreated;
use App\Modules\Navigation\Events\NavigationDeleted;
use App\Modules\Navigation\Events\NavigationItemCreated;
use App\Modules\Navigation\Events\NavigationItemMoved;
use App\Modules\Navigation\Events\NavigationPublished;
use App\Modules\Navigation\Events\NavigationUpdated;
use App\Modules\Navigation\Exceptions\NavigationException;
use App\Modules\Navigation\Exceptions\NavigationNotFoundException;
use App\Modules\Navigation\Models\Navigation;
use App\Modules\Navigation\Models\NavigationItem;
use App\Modules\Navigation\Support\NavigationHooks;

/**
 * The public API of the Navigation Engine. Resolves cached, multi-locale trees
 * into a single locale + viewer (visibility-filtered), and owns the write side
 * (navigations/items) — dispatching domain events while the cache stays coherent
 * through model-event invalidation. Sub-concerns live in dedicated collaborators;
 * this manager orchestrates them.
 */
final class NavigationManager implements NavigationManagerInterface
{
    public function __construct(
        private readonly NavigationRepositoryInterface $repository,
        private readonly NavigationCacheInterface $cache,
        private readonly NavigationVisibilityEvaluatorInterface $visibility,
        private readonly NavigationSourceRegistryInterface $sources,
        private readonly NavigationGeneratorRegistryInterface $generators,
        private readonly NavigationTypeRegistryInterface $types,
        private readonly EventBusInterface $events,
        private readonly HookManagerInterface $hooks,
    ) {}

    public function tree(string $handle, ?string $locale = null, ?User $user = null): NavigationTree
    {
        $navigation = $this->repository->findByHandle($handle);

        if ($navigation === null) {
            throw NavigationNotFoundException::handle($handle);
        }

        $locale ??= app()->getLocale();
        $user ??= $this->actingUser();

        $visible = $this->filterByVisibility($this->cache->get($handle) ?? [], $user);
        $nodes = $this->toNodes($visible, $locale);

        $filtered = $this->hooks->applyFilters(NavigationHooks::MODIFY_TREE, $nodes, $navigation, $locale, $user);
        if (is_array($filtered)) {
            $nodes = array_values(array_filter($filtered, static fn (mixed $node): bool => $node instanceof NavigationNode));
        }

        return new NavigationTree($handle, $navigation->type, $locale, $nodes);
    }

    public function has(string $handle): bool
    {
        return $this->repository->findByHandle($handle) !== null;
    }

    /**
     * @return list<Navigation>
     */
    public function navigations(): array
    {
        return array_values(Navigation::query()->orderBy('name')->get()->all());
    }

    public function createNavigation(NavigationData $data): Navigation
    {
        $this->assertKnownType($data->type);

        $navigation = Navigation::query()->create($data->toAttributes());

        $this->events->dispatch(new NavigationCreated($navigation));

        return $navigation;
    }

    public function updateNavigation(Navigation $navigation, NavigationData $data): Navigation
    {
        $this->assertKnownType($data->type);

        $navigation->update($data->toAttributes());

        $this->events->dispatch(new NavigationUpdated($navigation));

        return $navigation;
    }

    public function deleteNavigation(Navigation $navigation): void
    {
        $id = $navigation->id;
        $handle = $navigation->handle;

        $navigation->delete();

        $this->events->dispatch(new NavigationDeleted($id, $handle));
    }

    public function publishNavigation(Navigation $navigation): Navigation
    {
        $navigation->update(['is_active' => true]);

        $this->events->dispatch(new NavigationPublished($navigation));

        return $navigation;
    }

    public function addItem(NavigationItemData $data): NavigationItem
    {
        $item = NavigationItem::query()->create($data->toAttributes());

        $this->events->dispatch(new NavigationItemCreated($item));

        return $item;
    }

    public function moveItem(NavigationItem $item, ?string $parentId, int $order): NavigationItem
    {
        if ($parentId !== null && $this->wouldCreateCycle($item, $parentId)) {
            throw new NavigationException("Cannot move navigation item [{$item->id}] under itself or one of its descendants.");
        }

        $previousParentId = $item->parent_id;
        $previousOrder = $item->order;

        $item->update(['parent_id' => $parentId, 'order' => $order]);

        $this->events->dispatch(new NavigationItemMoved($item, $previousParentId, $previousOrder));

        return $item;
    }

    public function removeItem(NavigationItem $item): void
    {
        $item->delete();
    }

    public function registerType(NavigationTypeDefinition $type): void
    {
        $this->types->register($type);
    }

    public function registerSource(NavigationSourceResolverInterface $resolver): void
    {
        $this->sources->register($resolver);
    }

    public function registerGenerator(NavigationGeneratorInterface $generator): void
    {
        $this->generators->register($generator);
    }

    public function warm(): void
    {
        $this->cache->warm();
    }

    public function flush(): void
    {
        $this->cache->flush();
    }

    private function actingUser(): ?User
    {
        $user = auth()->user();

        return $user instanceof User ? $user : null;
    }

    private function assertKnownType(string $type): void
    {
        if (! $this->types->has($type)) {
            throw new NavigationException("Unknown navigation type [{$type}].");
        }
    }

    /**
     * True when re-parenting $item under $parentId would create a cycle — i.e.
     * $item is the target parent itself or one of its ancestors.
     */
    private function wouldCreateCycle(NavigationItem $item, string $parentId): bool
    {
        $cursorId = $parentId;

        while ($cursorId !== null) {
            if ($cursorId === $item->id) {
                return true;
            }

            $cursorId = NavigationItem::query()->find($cursorId)?->parent_id;
        }

        return false;
    }

    /**
     * @param  list<array<string, mixed>>  $nodes
     * @return list<array<string, mixed>>
     */
    private function filterByVisibility(array $nodes, ?User $user): array
    {
        $visible = [];

        foreach ($nodes as $node) {
            $mode = is_string($node['visibility'] ?? null)
                ? (NavigationVisibility::tryFrom($node['visibility']) ?? NavigationVisibility::Public)
                : NavigationVisibility::Public;

            if (! $this->visibility->isVisible($mode, $this->stringList($node['visibility_rules'] ?? []), $user)) {
                continue;
            }

            $node['children'] = $this->filterByVisibility($this->nodeList($node['children'] ?? []), $user);
            $visible[] = $node;
        }

        return $visible;
    }

    /**
     * @param  list<array<string, mixed>>  $nodes
     * @return list<NavigationNode>
     */
    private function toNodes(array $nodes, string $locale): array
    {
        $result = [];

        foreach ($nodes as $node) {
            $converted = $this->toNode($node, $locale);
            if ($converted !== null) {
                $result[] = $converted;
            }
        }

        return $result;
    }

    /**
     * @param  array<string, mixed>  $node
     */
    private function toNode(array $node, string $locale): ?NavigationNode
    {
        $children = $this->toNodes($this->nodeList($node['children'] ?? []), $locale);

        $urls = is_array($node['urls'] ?? null) ? $node['urls'] : [];
        $url = is_string($urls[$locale] ?? null) ? $urls[$locale] : null;

        // A node with neither a URL for this locale nor any visible child is not
        // reachable here (e.g. an entry not translated for the locale) — drop it.
        if ($url === null && $children === []) {
            return null;
        }

        return new NavigationNode(
            id: is_string($node['id'] ?? null) ? $node['id'] : '',
            label: $this->localizedLabel(is_array($node['labels'] ?? null) ? $node['labels'] : [], $locale),
            url: $url,
            target: is_string($node['target'] ?? null) ? $node['target'] : '_self',
            sourceType: is_string($node['source_type'] ?? null) ? $node['source_type'] : null,
            active: (bool) ($node['active'] ?? true),
            meta: is_array($node['meta'] ?? null) ? $node['meta'] : [],
            children: $children,
        );
    }

    /**
     * @param  array<array-key, mixed>  $labels
     */
    private function localizedLabel(array $labels, string $locale): string
    {
        foreach ([$locale, 'tg'] as $key) {
            if (is_string($labels[$key] ?? null)) {
                return $labels[$key];
            }
        }

        return '';
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function nodeList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter($value, static fn (mixed $node): bool => is_array($node)));
    }

    /**
     * @return list<string>
     */
    private function stringList(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return array_values(array_filter(array_map(
            static fn (mixed $rule): ?string => is_string($rule) ? $rule : null,
            $value,
        ), static fn (?string $rule): bool => $rule !== null));
    }
}
