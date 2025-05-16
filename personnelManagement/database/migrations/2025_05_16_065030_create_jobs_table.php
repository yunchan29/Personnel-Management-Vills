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
    Schema::create('jobs', function (Blueprint $table) {
        $table->id();
        $table->string('job_title');
        $table->string('company_name');
        $table->string('location');
        $table->integer('vacancies');
        $table->date('apply_until');
        $table->text('qualifications')->nullable();
        $table->text('additional_info')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
