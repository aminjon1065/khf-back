<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->nullable()->constrained('news')->nullOnDelete();
            $table->json('category')->nullable();
            $table->json('title');
            $table->string('date')->nullable();
            $table->string('source')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slides');
    }
};
