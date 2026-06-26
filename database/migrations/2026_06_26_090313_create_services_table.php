<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('key')->nullable();
            $table->string('icon')->nullable();
            $table->json('title');
            $table->json('subtitle')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->string('tel')->nullable();
            $table->string('route_key')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
