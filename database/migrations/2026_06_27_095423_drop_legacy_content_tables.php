<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('slides');
        Schema::dropIfExists('services');
        Schema::dropIfExists('news');
        Schema::dropIfExists('news_categories');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('document_categories');
        Schema::dropIfExists('forum_topics');
        Schema::dropIfExists('forum_categories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // One way migration, no turning back!
    }
};
