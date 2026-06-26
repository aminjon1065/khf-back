<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_category_id')->nullable()->constrained()->nullOnDelete();
            $table->json('title');
            $table->string('author')->nullable();
            $table->unsignedInteger('replies')->default(0);
            $table->unsignedInteger('views')->default(0);
            $table->boolean('pinned')->default(false);
            $table->string('last_activity')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_topics');
    }
};
