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
        Schema::table('applications', function (Blueprint $table) {
            // Add unique constraint to prevent duplicate applications
            // A user can only apply once to each job
            $table->unique(['user_id', 'job_id'], 'unique_user_job_application');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Remove the unique constraint
            $table->dropUnique('unique_user_job_application');
        });
    }
};
