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
    Schema::table('applications', function (Blueprint $table) {
        $table->string('training_schedule')->nullable()->after('interview_date'); // or wherever it fits logically
    });
}

public function down()
{
    Schema::table('applications', function (Blueprint $table) {
        $table->dropColumn('training_schedule');
    });
}

};
