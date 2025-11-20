<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDetailFieldsToAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {

        // 新しく必要なカラムだけ追加
            if (!Schema::hasColumn('attendances', 'break_time')) {
                $table->integer('break_time')->nullable()->after('break_started_at'); // 単位: 分
            }
            if (!Schema::hasColumn('attendances', 'work_time')) {
                $table->integer('work_time')->nullable()->after('break_time'); // 単位: 分
            }
            if (!Schema::hasColumn('attendances', 'note')) {
                $table->text('note')->nullable()->after('work_time');
            }
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
            $table->dropColumn(['break_time', 'work_time', 'note']);
        });
    }
}
