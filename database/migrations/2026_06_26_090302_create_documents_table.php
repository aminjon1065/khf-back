<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->unique();
            $table->json('title');
            $table->string('number')->nullable();
            $table->date('document_date')->nullable();
            $table->string('type', 8)->default('PDF');
            $table->string('size')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
