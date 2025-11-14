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
        Schema::table('jobs', function (Blueprint $table) {
            // Remove 'draft' from status enum (keep: active, expired, filled)
            \DB::statement("ALTER TABLE jobs MODIFY COLUMN status ENUM('active', 'expired', 'filled') DEFAULT 'active'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            // Restore 'draft' to status enum
            \DB::statement("ALTER TABLE jobs MODIFY COLUMN status ENUM('active', 'expired', 'draft', 'filled') DEFAULT 'active'");
        });
    }
};
