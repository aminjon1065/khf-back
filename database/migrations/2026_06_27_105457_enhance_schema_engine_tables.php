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
            $table->foreignId('updated_by')->nullable()->after('author_id')->constrained('users')->nullOnDelete();
            $table->index(['collection_id', 'status', 'published_at'], 'entries_collection_status_published_index');
        });
    }

    public function down(): void
    {
        // The composite index leads with collection_id, so MySQL uses it to back
        // the collection_id foreign key, making the index undroppable while the FK
        // exists. Lift the FK first, drop the index, then restore the FK — which
        // recreates its own backing index, returning the table to its prior shape.
        Schema::table('entries', function (Blueprint $table) {
            $table->dropForeign(['collection_id']);
        });

        Schema::table('entries', function (Blueprint $table) {
            $table->dropIndex('entries_collection_status_published_index');
            $table->dropConstrainedForeignId('updated_by');
            $table->dropColumn('version');

            $table->foreign('collection_id')->references('id')->on('collections')->cascadeOnDelete();
        });
    }
};
