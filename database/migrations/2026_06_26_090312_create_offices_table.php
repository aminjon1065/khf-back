<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->json('region');
            $table->json('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->json('hours')->nullable();
            $table->boolean('is_head')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
