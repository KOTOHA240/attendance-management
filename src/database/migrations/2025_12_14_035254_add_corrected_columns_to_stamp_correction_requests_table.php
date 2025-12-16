<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCorrectedColumnsToStampCorrectionRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stamp_correction_requests', function (Blueprint $table) {
            $table->time('corrected_start_time')->nullable()->after('target_date');
            $table->time('corrected_end_time')->nullable()->after('corrected_start_time');
            $table->time('corrected_break_start_time')->nullable()->after('corrected_end_time');
            $table->time('corrected_break_end_time')->nullable()->after('corrected_break_start_time');
            $table->text('note')->nullable()->after('reason');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stamp_correction_requests', function (Blueprint $table) {
            $table->dropColumn([
                'corrected_start_time',
                'corrected_end_time',
                'corrected_break_start_time',
                'corrected_break_end_time',
                'note',
            ]);
        });
    }
}
