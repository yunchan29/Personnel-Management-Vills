<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Remove redundant File201 fields from applications table.
     * This data is already stored in the file201s table via user relationship.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'sss_number',
                'philhealth_number',
                'tin_id_number',
                'pagibig_number',
                'licenses',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Restore columns if migration is rolled back
            $table->string('sss_number')->nullable();
            $table->string('philhealth_number')->nullable();
            $table->string('tin_id_number')->nullable();
            $table->string('pagibig_number')->nullable();
            $table->json('licenses')->nullable();
        });
    }
};
