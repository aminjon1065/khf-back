<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Replaces the unused Spatie-shaped `media` table with the canonical KHF Media
 * Engine schema (a standalone DAM) plus a relational `media_conversions` table
 * tracking each derived file (thumbnails, WebP/AVIF, responsive widths).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('media');

        Schema::create('media', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Storage location
            $table->string('driver')->default('local'); // pluggable storage driver
            $table->string('disk');                      // Laravel filesystem disk
            $table->string('path');                      // key within the disk
            $table->string('visibility')->default('public');

            // Identity
            $table->string('name')->nullable();          // human title
            $table->string('file_name');                 // stored file name
            $table->string('original_file_name');        // name at upload time
            $table->string('mime_type');
            $table->string('extension')->nullable();
            $table->unsignedBigInteger('size');          // bytes

            // Intrinsic media metadata
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedInteger('duration')->nullable(); // seconds, for a/v
            $table->string('checksum', 64)->nullable();      // sha256

            // Editable descriptive metadata
            $table->string('alt_text')->nullable();
            $table->text('caption')->nullable();
            $table->string('copyright')->nullable();
            $table->json('focal_point')->nullable();         // {x, y} 0..1
            $table->string('dominant_color')->nullable();    // hex
            $table->json('exif')->nullable();
            $table->json('custom_properties')->nullable();   // extensible bag

            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index('checksum');
            $table->index('mime_type');
            $table->index(['disk', 'visibility']);
        });

        Schema::create('media_conversions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('media_id')->constrained('media')->cascadeOnDelete();
            $table->string('conversion_name');
            $table->string('driver')->default('local');
            $table->string('disk');
            $table->string('path');
            $table->string('visibility')->default('public');
            $table->string('mime_type');
            $table->string('format'); // webp, avif, jpg, ...
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedBigInteger('size');
            $table->timestamps();

            $table->unique(['media_id', 'conversion_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_conversions');
        Schema::dropIfExists('media');

        // Restore the prior Spatie-shaped table so this migration is reversible.
        Schema::create('media', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuidMorphs('model');
            $table->uuid('uuid')->nullable()->unique();
            $table->string('collection_name');
            $table->string('name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('conversions_disk')->nullable();
            $table->unsignedBigInteger('size');
            $table->json('manipulations');
            $table->json('custom_properties');
            $table->json('generated_conversions');
            $table->json('responsive_images');
            $table->unsignedInteger('order_column')->nullable()->index();
            $table->nullableTimestamps();
            $table->softDeletes();
        });
    }
};
