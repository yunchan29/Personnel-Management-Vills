
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('training_schedules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('scheduled_by')->nullable()->constrained('users')->onDelete('set null');

            $table->date('start_date');
            $table->date('end_date');

            $table->enum('status', ['scheduled','rescheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_schedules');
    }
};
