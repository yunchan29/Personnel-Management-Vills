<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Applications table indexes
        // Note: user_id and job_id already have indexes via foreign key constraints
        Schema::table('applications', function (Blueprint $table) {
            $table->index('status', 'idx_applications_status');
            $table->index('is_archived', 'idx_applications_is_archived');
            $table->index('created_at', 'idx_applications_created_at');
        });

        // Jobs table indexes
        Schema::table('jobs', function (Blueprint $table) {
            $table->index('apply_until', 'idx_jobs_apply_until');
            $table->index('job_industry', 'idx_jobs_job_industry');
            $table->index('created_at', 'idx_jobs_created_at');
        });

        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->index('role', 'idx_users_role');
            $table->index('email_verified_at', 'idx_users_email_verified_at');
            $table->index('job_industry', 'idx_users_job_industry');
        });

        // Leave forms table indexes
        // Note: user_id already has an index via foreign key constraint
        Schema::table('leave_forms', function (Blueprint $table) {
            $table->index('status', 'idx_leave_forms_status');
            $table->index('created_at', 'idx_leave_forms_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop Applications indexes
        // Note: user_id and job_id indexes are managed by foreign key constraints
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex('idx_applications_status');
            $table->dropIndex('idx_applications_is_archived');
            $table->dropIndex('idx_applications_created_at');
        });

        // Drop Jobs indexes
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropIndex('idx_jobs_apply_until');
            $table->dropIndex('idx_jobs_job_industry');
            $table->dropIndex('idx_jobs_created_at');
        });

        // Drop Users indexes
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role');
            $table->dropIndex('idx_users_email_verified_at');
            $table->dropIndex('idx_users_job_industry');
        });

        // Drop Leave forms indexes
        // Note: user_id index is managed by foreign key constraint
        Schema::table('leave_forms', function (Blueprint $table) {
            $table->dropIndex('idx_leave_forms_status');
            $table->dropIndex('idx_leave_forms_created_at');
        });
    }
};
