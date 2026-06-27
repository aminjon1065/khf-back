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
        Schema::dropIfExists('leaders');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('directions');
        Schema::dropIfExists('programs');
    }

    public function down(): void
    {
        // Reversing this would require recreating tables, which is not strictly necessary in forward-only migrations.
        // We'll leave it empty to prevent error when rolling back.
    }
};
