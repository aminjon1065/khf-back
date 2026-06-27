<?php

declare(strict_types=1);

namespace App\Modules\Navigation\Services;

use App\Modules\Navigation\Contracts\NavigationGeneratorRegistryInterface;
use App\Modules\Navigation\Contracts\NavigationRepositoryInterface;
use App\Modules\Navigation\Contracts\NavigationSourceRegistryInterface;
use App\Modules\Navigation\Contracts\NavigationTreeBuilderInterface;
use App\Modules\Navigation\DTOs\GeneratedNavigationItem;
use App\Modules\Navigation\Enums\NavigationVisibility;
use App\Modules\Navigation\Models\Navigation;
use App\Modules\Navigation\Models\NavigationItem;
use App\Modules\Navigation\Support\NavigationLocales;

/**
 * Assembles every active navigation into a fully-resolved, multi-locale tree of
 * plain node arrays. The adjacency list (parent_id) is folded into a nested tree
 * in memory; each item's URL is resolved for every locale and dynamic items are
 * expanded into generated children. The result is cacheable and locale-/viewer-
 * agnostic — selection and visibility filtering happen later, at read time.
 */
final class NavigationTreeBuilder implements NavigationTreeBuilderInterface
{
    public function __construct(
        private readonly NavigationRepositoryInterface $repository,
        private readonly NavigationSourceRegistryInterface $sources,
        private readonly NavigationGeneratorRegistryInterface $generators,
        private readonly NavigationSourceCache $sourceCache,
    ) {}

    /**
     * @return array<string, list<array<string, mixed>>>
     */
    public function buildAll(): array
    {
        $this->sourceCache->reset();

        $locales = NavigationLocales::all();
        $result = [];

        foreach ($this->repository->allActiveWithItems() as $navigation) {
            $result[$navigation->handle] = $this->buildTree($navigation, $locales);
        }

        return $result;
    }

    /**
     * Folds the adjacency list into a nested tree, descending only from roots.
     * Because every item has a single parent_id, each node is built at most once,
     * so a parent_id cycle simply leaves its members unreachable (never an
     * infinite loop). Deactivating a parent (is_active=false) removes it from
     * activeItems, so its whole subtree is intentionally hidden.
     *
     * @param  list<string>  $locales
     * @return list<array<string, mixed>>
     */
    private function buildTree(Navigation $navigation, array $locales): array
    {
        /** @var array<string, list<NavigationItem>> $byParent */
        $byParent = [];

        foreach ($navigation->activeItems as $item) {
            $byParent[$item->parent_id ?? ''][] = $item;
        }

        return $this->buildChildren($byParent, '', $locales);
    }

    /**
     * @param  array<string, list<NavigationItem>>  $byParent
     * @param  list<string>  $locales
     * @return list<array<string, mixed>>
     */
    private function buildChildren(array $byParent, string $parentKey, array $locales): array
    {
        $nodes = [];

        foreach ($byParent[$parentKey] ?? [] as $item) {
            $nodes[] = $this->buildNode($item, $byParent, $locales);
        }

        return $nodes;
    }

    /**
     * @param  array<string, list<NavigationItem>>  $byParent
     * @param  list<string>  $locales
     * @return array<string, mixed>
     */
    private function buildNode(NavigationItem $item, array $byParent, array $locales): array
    {
        $children = $this->buildChildren($byParent, $item->id, $locales);

        if ($item->generator !== null && $this->generators->has($item->generator)) {
            foreach ($this->generators->get($item->generator)->generate($item) as $generated) {
                $children[] = $this->generatedNode($generated);
            }
        }

        return [
            'id' => $item->id,
            'labels' => $this->labels($item->label, $locales),
            'urls' => $this->resolveUrls($item, $locales),
            'target' => $item->target,
            'source_type' => $item->source_type?->value,
            'visibility' => $item->visibility->value,
            'visibility_rules' => $item->visibility_rules ?? [],
            'active' => $item->is_active,
            'meta' => $item->meta ?? [],
            'children' => $children,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function generatedNode(GeneratedNavigationItem $generated): array
    {
        $entryId = $generated->meta['entry_id'] ?? null;
        $id = is_string($entryId)
            ? 'gen:'.$entryId
            : 'gen:'.md5(implode('|', array_map(static fn (mixed $url): string => (string) $url, $generated->url)));

        return [
            'id' => $id,
            'labels' => $generated->label,
            'urls' => $generated->url,
            'target' => $generated->target,
            'source_type' => null,
            'visibility' => NavigationVisibility::Public->value,
            'visibility_rules' => [],
            'active' => true,
            'meta' => $generated->meta,
            'children' => [],
        ];
    }

    /**
     * @param  list<string>  $locales
     * @return array<string, string|null>
     */
    private function resolveUrls(NavigationItem $item, array $locales): array
    {
        $type = $item->source_type?->value;

        if ($type === null || ! $this->sources->has($type)) {
            return [];
        }

        $resolver = $this->sources->get($type);
        $urls = [];

        foreach ($locales as $locale) {
            $urls[$locale] = $resolver->resolve($item, $locale);
        }

        return $urls;
    }

    /**
     * @param  array<string, string>  $label
     * @param  list<string>  $locales
     * @return array<string, string>
     */
    private function labels(array $label, array $locales): array
    {
        $fallback = is_string($label['tg'] ?? null) ? $label['tg'] : '';
        $resolved = [];

        foreach ($locales as $locale) {
            $value = $label[$locale] ?? null;
            $resolved[$locale] = is_string($value) ? $value : $fallback;
        }

        return $resolved;
    }
}
