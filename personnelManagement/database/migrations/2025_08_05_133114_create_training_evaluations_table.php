<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('training_evaluations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluated_by')->constrained('users')->onDelete('cascade');

            $table->unsignedTinyInteger('knowledge');       // max 30
            $table->unsignedTinyInteger('skill');           // max 30
            $table->unsignedTinyInteger('participation');   // max 20
            $table->unsignedTinyInteger('professionalism'); // max 20

            $table->unsignedTinyInteger('total_score');     // out of 100
            $table->enum('result', ['Passed', 'Failed']);
            $table->timestamp('evaluated_at');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_evaluations');
    }
};
