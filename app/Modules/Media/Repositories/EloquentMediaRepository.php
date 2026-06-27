<?php

declare(strict_types=1);

namespace App\Modules\Media\Repositories;

use App\Modules\Media\Contracts\MediaRepositoryInterface;
use App\Modules\Media\Exceptions\MediaNotFoundException;
use App\Modules\Media\Models\Media;
use Illuminate\Pagination\LengthAwarePaginator;

final class EloquentMediaRepository implements MediaRepositoryInterface
{
    public function find(string $id): ?Media
    {
        return Media::find($id);
    }

    public function findOrFail(string $id): Media
    {
        $media = Media::find($id);

        if ($media === null) {
            throw MediaNotFoundException::withId($id);
        }

        return $media;
    }

    public function findByChecksum(string $checksum): ?Media
    {
        return Media::where('checksum', $checksum)->first();
    }

    public function save(Media $media): Media
    {
        $media->save();

        return $media;
    }

    public function delete(Media $media, bool $permanently = false): void
    {
        if ($permanently) {
            $media->forceDelete();

            return;
        }

        $media->delete();
    }

    public function restore(Media $media): Media
    {
        $media->restore();

        return $media;
    }

    /**
     * @return LengthAwarePaginator<int, Media>
     */
    public function paginate(int $perPage = 24): LengthAwarePaginator
    {
        return Media::query()->latest()->paginate($perPage);
    }
}
