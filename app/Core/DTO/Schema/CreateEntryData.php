<?php

declare(strict_types=1);

namespace App\Core\DTO\Schema;

use App\Core\DTO\DataTransferObject;
use App\Core\Enums\EntryStatus;
use Carbon\CarbonImmutable;

final class CreateEntryData extends DataTransferObject
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function __construct(
        public readonly string $collectionId,
        public readonly string $blueprintId,
        public readonly EntryStatus $status,
        public readonly array $data,
        public readonly ?int $authorId = null,
        public readonly ?string $slug = null,
        public readonly ?CarbonImmutable $publishedAt = null,
    ) {}

    public function withSlug(string $slug): self
    {
        return new self(
            collectionId: $this->collectionId,
            blueprintId: $this->blueprintId,
            status: $this->status,
            data: $this->data,
            authorId: $this->authorId,
            slug: $slug,
            publishedAt: $this->publishedAt,
        );
    }

    public function withPublishedAt(?CarbonImmutable $publishedAt): self
    {
        return new self(
            collectionId: $this->collectionId,
            blueprintId: $this->blueprintId,
            status: $this->status,
            data: $this->data,
            authorId: $this->authorId,
            slug: $this->slug,
            publishedAt: $publishedAt,
        );
    }
}
