<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Modules\Localization\Models\Locale;
use Illuminate\Database\Seeder;

/**
 * Seeds the bootstrap locale registry from `config('khf.localization.seed')`.
 * Idempotent — matches on `code` so re-running updates existing rows in place.
 */
final class LocaleSeeder extends Seeder
{
    public function run(): void
    {
        /** @var list<array<string, mixed>> $seed */
        $seed = config('khf.localization.seed', []);

        foreach ($seed as $row) {
            Locale::query()->updateOrCreate(['code' => $row['code']], $row);
        }
    }
}
