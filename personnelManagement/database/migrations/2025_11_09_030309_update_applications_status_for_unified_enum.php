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
        // Change status column to string type to support all enum values
        Schema::table('applications', function (Blueprint $table) {
            $table->string('status', 50)->default('pending')->change();
        });

        // Normalize existing status values to match the new enum format
        $statusMappings = [
            'Pending' => 'pending',
            'To Review' => 'to_review',
            'approved' => 'approved',
            'declined' => 'declined',
            'for_interview' => 'for_interview',
            'interviewed' => 'interviewed',
            'fail_interview' => 'failed_interview',
            'scheduled_for_training' => 'scheduled_for_training',
            'trained' => 'trained',
            'passed' => 'passed_evaluation',
            'failed' => 'failed_evaluation',
            'hired' => 'hired',
        ];

        foreach ($statusMappings as $old => $new) {
            DB::table('applications')
                ->where('status', $old)
                ->update(['status' => $new]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert status values back to original format
        $statusMappings = [
            'pending' => 'Pending',
            'to_review' => 'To Review',
            'failed_interview' => 'fail_interview',
            'passed_evaluation' => 'passed',
            'failed_evaluation' => 'failed',
        ];

        foreach ($statusMappings as $new => $old) {
            DB::table('applications')
                ->where('status', $new)
                ->update(['status' => $old]);
        }

        // Change status column back
        Schema::table('applications', function (Blueprint $table) {
            $table->string('status')->default('Pending')->change();
        });
    }
};
