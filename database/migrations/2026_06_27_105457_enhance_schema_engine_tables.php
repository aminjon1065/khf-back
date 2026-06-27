<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->unsignedInteger('version')->default(1)->after('data');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->after('author_id');
            $table->index(['collection_id', 'status', 'published_at'], 'entries_collection_status_published_index');
        });
    }

    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropIndex('entries_collection_status_published_index');
            $table->dropConstrainedForeignId('updated_by');
            $table->dropColumn('version');
        });
    }
};
