<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('resumes', function (Blueprint $table) {
            // Drop foreign key if it exists, ignore error if not
            try {
                $table->dropForeign(['job_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist, ignore error
            }
        });

        Schema::table('resumes', function (Blueprint $table) {
            // Make job_id nullable
            $table->unsignedBigInteger('job_id')->nullable()->change();
        });

        Schema::table('resumes', function (Blueprint $table) {
            // Recreate foreign key (nullable allowed)
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // To avoid errors on rollback, drop foreign key only if it exists
        $foreignKeyExists = DB::select("
            SELECT CONSTRAINT_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_NAME = 'resumes'
              AND COLUMN_NAME = 'job_id'
              AND CONSTRAINT_SCHEMA = DATABASE()
              AND REFERENCED_TABLE_NAME = 'jobs'
        ");

        Schema::table('resumes', function (Blueprint $table) use ($foreignKeyExists) {
            if (!empty($foreignKeyExists)) {
                $table->dropForeign(['job_id']);
            }
            // Make job_id NOT nullable again
            $table->unsignedBigInteger('job_id')->nullable(false)->change();

            // Recreate foreign key
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
        });
    }
};
