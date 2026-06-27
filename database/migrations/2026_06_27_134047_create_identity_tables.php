<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Identity & Access Management schema.
 *
 * Extends the existing users/roles/permissions tables (additive — keeps the
 * bigint PK so Sprint 2/3 foreign keys stay intact; UUID is a secondary public
 * identifier) and adds user_profiles + an immutable activities log.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->unique()->after('id');
            $table->string('locale')->nullable()->after('email_verified_at');
            $table->string('timezone')->nullable()->after('locale');
            $table->string('status')->default('active')->after('timezone');
            $table->foreignUuid('avatar_media_id')->nullable()->after('status')->constrained('media')->nullOnDelete();
            $table->timestamp('last_login_at')->nullable()->after('avatar_media_id');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            $table->softDeletes();
        });

        // Backfill UUIDs for any existing users.
        foreach (DB::table('users')->whereNull('uuid')->pluck('id') as $id) {
            DB::table('users')->where('id', $id)->update(['uuid' => (string) Str::uuid()]);
        }

        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('is_system')->default(false)->after('guard_name');
            $table->string('description')->nullable()->after('is_system');
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->string('description')->nullable()->after('guard_name');
            $table->string('category')->nullable()->after('description');
        });

        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->text('bio')->nullable();
            $table->string('phone')->nullable();
            $table->string('job_title')->nullable();
            $table->json('meta')->nullable(); // extensible profile fields (hook target)
            $table->timestamps();
        });

        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type');                        // e.g. auth.login, user.created, role.assigned
            $table->string('description')->nullable();
            $table->string('subject_type')->nullable();    // string id supports both bigint + uuid subjects
            $table->string('subject_id')->nullable();
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->nullable();   // immutable log — no updated_at

            $table->index('type');
            $table->index('created_at');
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
        Schema::dropIfExists('user_profiles');

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['description', 'category']);
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['is_system', 'description']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('avatar_media_id');
            $table->dropSoftDeletes();
            $table->dropColumn(['uuid', 'locale', 'timezone', 'status', 'last_login_at', 'last_login_ip']);
        });
    }
};
