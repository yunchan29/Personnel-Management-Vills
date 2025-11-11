<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('status')->default('To Review')->change();
        });

        // Optionally update existing records
        DB::table('applications')
            ->where('status', 'Pending')
            ->update(['status' => 'To Review']);
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->string('status')->default('Pending')->change();
        });

        // Optionally revert existing records
        DB::table('applications')
            ->where('status', 'To Review')
            ->update(['status' => 'Pending']);
    }
};
