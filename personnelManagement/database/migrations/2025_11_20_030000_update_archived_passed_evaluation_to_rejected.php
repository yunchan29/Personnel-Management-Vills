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
        // Update all archived applications with 'passed_evaluation' status to 'rejected'
        DB::table('applications')
            ->where('is_archived', true)
            ->where('status', 'passed_evaluation')
            ->update(['status' => 'rejected']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert rejected status back to passed_evaluation for archived applications
        // Note: This is a best-effort rollback and may not be 100% accurate
        // if there were already archived rejected applications before this migration
        DB::table('applications')
            ->where('is_archived', true)
            ->where('status', 'rejected')
            ->update(['status' => 'passed_evaluation']);
    }
};
