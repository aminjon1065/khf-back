<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_category_id')
                ->nullable()
                ->constrained('news_categories')
                ->nullOnDelete();
            $table->string('slug')->unique();
            $table->json('title');          // переводимое
            $table->json('excerpt')->nullable(); // переводимое
            $table->json('body')->nullable();    // переводимый rich-text (HTML)
            $table->string('author')->nullable();
            $table->string('region')->nullable();
            $table->unsignedBigInteger('views')->default(0);
            $table->string('status', 16)->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
