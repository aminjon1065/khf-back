<?php

namespace App\Http\Requests\Content;

use App\Core\DTO\Schema\CreateEntryData;
use App\Core\DTO\Schema\UpdateEntryData;
use App\Core\Enums\EntryStatus;
use App\Core\Models\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // access restricted by permission middleware on the route
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'status' => ['required', Rule::enum(EntryStatus::class)],
            'data' => ['required', 'array'],
        ];

        // blueprint_id is only required when creating a new entry, and it must
        // belong to the collection bound on the route.
        if ($this->route('entry') === null) {
            $collection = $this->route('collection');
            $collectionId = $collection instanceof Collection ? $collection->id : null;

            $rules['blueprint_id'] = [
                'required',
                'string',
                Rule::exists('blueprints', 'id')->where(fn (Builder $query): Builder => $query->where('collection_id', $collectionId)),
            ];
        }

        return $rules;
    }

    public function toCreateData(Collection $collection, ?int $authorId): CreateEntryData
    {
        $validated = $this->validated();

        /** @var array<string, mixed> $data */
        $data = (array) $validated['data'];

        return new CreateEntryData(
            collectionId: $collection->id,
            blueprintId: (string) $validated['blueprint_id'],
            status: EntryStatus::from((string) $validated['status']),
            data: $data,
            authorId: $authorId,
            slug: $this->titleCandidate($data),
        );
    }

    public function toUpdateData(?int $updatedBy): UpdateEntryData
    {
        $validated = $this->validated();

        /** @var array<string, mixed> $data */
        $data = (array) $validated['data'];

        return new UpdateEntryData(
            data: $data,
            status: EntryStatus::from((string) $validated['status']),
            updatedBy: $updatedBy,
        );
    }

    /**
     * Best-effort human title for slug generation. Localized titles are a
     * presentation concern, so the look-up lives here rather than in the engine.
     *
     * @param  array<string, mixed>  $data
     */
    private function titleCandidate(array $data): ?string
    {
        foreach (['global', 'tg', 'ru', 'en'] as $key) {
            $section = $data[$key] ?? null;

            if (is_array($section) && isset($section['title']) && is_string($section['title'])) {
                return $section['title'];
            }
        }

        return null;
    }
}
