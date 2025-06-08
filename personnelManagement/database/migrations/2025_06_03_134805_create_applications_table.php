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

    $table->string('resume')->nullable();
    $table->string('sss')->nullable();
    $table->string('philhealth')->nullable();
    $table->string('pagibig')->nullable();
    $table->string('tin')->nullable();

    $table->json('licenses')->nullable(); // Flexible

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
