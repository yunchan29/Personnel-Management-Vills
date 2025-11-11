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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();

            // Foreign Keys
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_id')->constrained()->onDelete('cascade');

            // Documents
            $table->string('resume_snapshot')->nullable();
            $table->json('licenses')->nullable();

            // Government IDs (stored as strings to preserve leading zeroes)
            $table->string('sss_number')->nullable();
            $table->string('philhealth_number')->nullable();
            $table->string('tin_id_number')->nullable();
            $table->string('pagibig_number')->nullable();

            // Status and Tracking
            $table->string('status', 50)->default('pending');
            $table->boolean('is_archived')->default(false);
            $table->dateTime('reviewed_at')->nullable();

            // Contract Information
            $table->dateTime('contract_signing_schedule')->nullable();
            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
