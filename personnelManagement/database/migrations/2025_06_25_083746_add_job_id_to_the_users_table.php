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
      Schema::table('users', function (Blueprint $table) {
    $table->unsignedBigInteger('job_id')->nullable()->after('role');
    $table->foreign('job_id')->references('id')->on('jobs')->onDelete('set null');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('the_users', function (Blueprint $table) {
            //
        });
    }
};
