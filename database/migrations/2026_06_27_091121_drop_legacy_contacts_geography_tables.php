<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('hotlines');
        Schema::dropIfExists('offices');
        Schema::dropIfExists('regions');
    }

    public function down(): void
    {
        // One way trip. We don't want to re-create the legacy tables.
    }
};
