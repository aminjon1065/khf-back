<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Extends the existing key/value `settings` table into the canonical Settings
 * Engine value store. Additive + nullable, so the legacy App\Models\Setting
 * singletons (president, site_stats, …) keep working unchanged. Setting
 * DEFINITIONS (defaults, validation, type) live in the in-memory registry; only
 * overridden values are persisted here.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('group')->nullable()->after('key');
            $table->string('type')->nullable()->after('group');

            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropIndex(['group']);
            $table->dropColumn(['group', 'type']);
        });
    }
};
