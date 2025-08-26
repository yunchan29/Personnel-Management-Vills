<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartAndEndDateToInterviewsTable extends Migration
{
    public function up()
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->dateTime('start_time')->nullable()->after('application_id');
            $table->dateTime('end_time')->nullable()->after('start_time');
            $table->dropColumn('scheduled_at'); // optional, if you don’t want it anymore
        });
    }

    public function down()
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->dateTime('scheduled_at')->nullable();
            $table->dropColumn(['start_time', 'end_time']);
        });
    }
}
