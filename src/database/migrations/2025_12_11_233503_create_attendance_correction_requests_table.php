<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceCorrectionRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_correction_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // 申請者
            $table->foreignId('attendance_id')->nullable()->constrained()->nullOnDelete(); // 紐づく勤怠
            $table->date('target_date'); // 対象日
            $table->text('reason')->nullable(); // 申請理由
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending'); // 状態
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_correction_requests');
    }
}
