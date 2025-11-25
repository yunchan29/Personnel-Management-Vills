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
        Schema::table('file201s', function (Blueprint $table) {
            $table->string('sss_file_path')->nullable()->after('sss_number');
            $table->string('philhealth_file_path')->nullable()->after('philhealth_number');
            $table->string('pagibig_file_path')->nullable()->after('pagibig_number');
            $table->string('tin_file_path')->nullable()->after('tin_id_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('file201s', function (Blueprint $table) {
            $table->dropColumn([
                'sss_file_path',
                'philhealth_file_path',
                'pagibig_file_path',
                'tin_file_path',
            ]);
        });
    }
};
