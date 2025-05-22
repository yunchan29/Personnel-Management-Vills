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
        Schema::create('file201s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Link to user
            $table->string('sss_number')->nullable();
            $table->string('philhealth_number')->nullable();
            $table->string('pagibig_number')->nullable();
            $table->string('tin_id_number')->nullable();
            $table->json('licenses')->nullable(); // Store license info as JSON
            $table->timestamps();

            // Foreign key constraint (optional, assuming you have a `users` table)
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file201s');
    }
};
