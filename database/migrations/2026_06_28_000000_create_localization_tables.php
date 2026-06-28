<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Localization Engine — the multilingual backbone. `locales` is the registry of
 * supported languages; `translations` holds group/key/locale string values; and
 * `localized_slugs` maps polymorphic content subjects to per-locale URL slugs.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locales', function (Blueprint $table): void {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('native_name');
            $table->string('direction', 3)->default('ltr');
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('fallback_code')->nullable();
            $table->string('alias')->nullable()->unique();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('translations', function (Blueprint $table): void {
            $table->id();
            $table->string('group')->index();
            $table->string('key');
            $table->string('locale', 12);
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['group', 'key', 'locale']);
            $table->index(['group', 'locale']);
        });

        Schema::create('localized_slugs', function (Blueprint $table): void {
            $table->id();
            $table->string('subject_type');
            $table->string('subject_id');
            $table->string('locale', 12);
            $table->string('slug');
            $table->boolean('is_canonical')->default(false);
            $table->timestamps();

            $table->unique(['subject_type', 'locale', 'slug']);
            $table->index(['subject_type', 'subject_id', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('localized_slugs');
        Schema::dropIfExists('translations');
        Schema::dropIfExists('locales');
    }
};
