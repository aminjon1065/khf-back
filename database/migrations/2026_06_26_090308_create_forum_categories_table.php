<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->json('title');
            $table->json('description')->nullable();
            $table->unsignedInteger('topics_count')->default(0);
            $table->unsignedInteger('posts_count')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_categories');
    }
};
