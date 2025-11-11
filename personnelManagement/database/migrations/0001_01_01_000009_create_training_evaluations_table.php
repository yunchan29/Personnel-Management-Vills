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
        Schema::create('training_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluated_by')->constrained('users')->onDelete('cascade');
            $table->unsignedTinyInteger('knowledge');
            $table->unsignedTinyInteger('skill');
            $table->unsignedTinyInteger('participation');
            $table->unsignedTinyInteger('professionalism');
            $table->unsignedTinyInteger('total_score');
            $table->enum('result', ['Passed', 'Failed']);
            $table->timestamp('evaluated_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_evaluations');
    }
};
