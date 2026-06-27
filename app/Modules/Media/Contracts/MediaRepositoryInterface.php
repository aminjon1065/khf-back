<?php

declare(strict_types=1);

namespace App\Modules\Media\Contracts;

use App\Modules\Media\Exceptions\MediaNotFoundException;
use App\Modules\Media\Models\Media;
use Illuminate\Pagination\LengthAwarePaginator;

interface MediaRepositoryInterface
{
    public function find(string $id): ?Media;

    /**
     * @throws MediaNotFoundException
     */
    public function findOrFail(string $id): Media;

    public function findByChecksum(string $checksum): ?Media;

    public function save(Media $media): Media;

    public function delete(Media $media, bool $permanently = false): void;

    public function restore(Media $media): Media;

    /**
     * @return LengthAwarePaginator<int, Media>
     */
    public function paginate(int $perPage = 24): LengthAwarePaginator;
}
