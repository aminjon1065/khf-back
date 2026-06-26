<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('directions', function (Blueprint $table) {
            $table->id();
            $table->string('key')->nullable();
            $table->string('icon')->nullable();
            $table->json('title');
            $table->json('description')->nullable();
            $table->string('stat_value')->nullable();
            $table->json('stat_label')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('directions');
    }
};
