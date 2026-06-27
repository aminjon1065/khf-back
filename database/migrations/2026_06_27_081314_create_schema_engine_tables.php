<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        Schema::create('blueprints', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('collection_id')->constrained('collections')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('fields', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('blueprint_id')->constrained('blueprints')->cascadeOnDelete();
            $table->string('name');
            $table->string('handle');
            $table->string('type');
            $table->boolean('is_translatable')->default(false);
            $table->json('validation_rules')->nullable();
            $table->json('settings')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['blueprint_id', 'handle']);
        });

        Schema::create('entries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('collection_id')->constrained('collections')->cascadeOnDelete();
            $table->foreignUuid('blueprint_id')->constrained('blueprints')->cascadeOnDelete();

            // Add author_id relation if users table exists, otherwise just uuid
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('status')->default('draft');
            $table->string('slug')->unique()->nullable();

            // Core JSONB data column for hybrid schema
            $table->jsonb('data')->nullable();

            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entries');
        Schema::dropIfExists('fields');
        Schema::dropIfExists('blueprints');
        Schema::dropIfExists('collections');
    }
};
