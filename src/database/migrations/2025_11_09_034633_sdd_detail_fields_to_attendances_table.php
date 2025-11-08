<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SddDetailFieldsToAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->date('date')->after('user_id');
            $table->time('start_time')->nullable()->after('date');
            $table->time('end_time')->nullable()->after('start_time');
            $table->time('break_start_time')->nullable()->after('end_time');
            $table->time('break_end_time')->nullable()->after('break_start_time');
            $table->integer('break_time')->nullable()->after('break_end_time'); // 単位: 分
            $table->integer('work_time')->nullable()->after('break_time'); // 単位: 分
            $table->text('note')->nullable()->after('work_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            //
        });
    }
}
