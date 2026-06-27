<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Navigation Engine — the canonical menu system. `navigations` are the menu
 * containers (header/footer/…); `navigation_items` form an unlimited-depth tree
 * via the self-referencing parent_id. Translatable labels and visibility rules
 * are stored as locale-/rule-keyed JSON.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('navigations', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('handle')->unique();
            $table->string('name');
            $table->string('type')->index();
            $table->string('description')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('navigation_items', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('navigation_id')->constrained('navigations')->cascadeOnDelete();
            $table->foreignUuid('parent_id')->nullable()->constrained('navigation_items')->cascadeOnDelete();
            $table->integer('order')->default(0);

            // Translatable label: {"tg": "...", "ru": "...", "en": "..."}.
            $table->json('label');

            // Where the item points. source_id holds a UUID for entry/collection
            // sources; source_value holds a URL / route name / page key. It is a
            // bare uuid (not a FK) because the target table varies by source_type.
            $table->string('source_type')->nullable();
            $table->uuid('source_id')->nullable();
            $table->string('source_value')->nullable();
            $table->string('target')->default('_self');

            // Visibility mode + the role/permission names it applies to.
            $table->string('visibility')->default('public');
            $table->json('visibility_rules')->nullable();

            // Dynamic generation: a non-null generator expands into child nodes at
            // build time using the config in `meta`.
            $table->string('generator')->nullable();
            $table->json('meta')->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['navigation_id', 'parent_id', 'order']);
            $table->index('source_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('navigation_items');
        Schema::dropIfExists('navigations');
    }
};
