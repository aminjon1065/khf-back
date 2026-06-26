<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->nullable();
            $table->string('type')->nullable();
            $table->string('region')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->string('phone')->nullable();
            $table->string('status', 16)->default('new');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
