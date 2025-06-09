<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
 public function up()
{
    Schema::create('applications', function (Blueprint $table) {
        $table->id();

        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('job_id')->constrained()->onDelete('cascade');
        $table->foreignId('resume_id')->nullable()->constrained()->onDelete('set null');

        $table->json('licenses')->nullable(); // Array of license paths or data

        // Government ID numbers (stored as strings to preserve leading zeroes)
        $table->string('sss_number')->nullable();
        $table->string('philhealth_number')->nullable();
        $table->string('tin_id_number')->nullable();
        $table->string('pagibig_number')->nullable();

        // ✅ Application tracking fields
        $table->enum('status', ['Pending', 'Under Review', 'Shortlisted', 'Interview Scheduled', 'Rejected', 'Hired'])
              ->default('Pending');

        $table->dateTime('interview_schedule')->nullable();
        $table->text('remarks')->nullable();
        $table->dateTime('reviewed_at')->nullable();

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
