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
       
        $table->date('birth_date')->nullable();
        $table->string('birth_place')->nullable();
        $table->integer('age')->nullable();
        $table->string('civil_status')->nullable();
        $table->string('religion')->nullable();
        $table->string('nationality')->nullable();
        $table->string('mobile_number')->nullable();
        $table->string('profile_picture')->nullable();
        $table->string('full_address')->nullable();
        $table->string('province')->nullable();
        $table->string('city')->nullable();
        $table->string('barangay')->nullable();
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn([
            'birth_date', 'birth_place', 'age','civil_status', 'religion', 'nationality', 'mobile_number',
            'profile_picture', 'full_address', 'province', 'city', 'barangay',
        ]);
    });
}

   };
