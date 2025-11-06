<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ユーザーID
            $table->string('status')->default('勤務外'); // 勤務状態
            $table->timestamp('started_at')->nullable(); // 出勤時刻
            $table->timestamp('break_started_at')->nullable(); // 休憩開始時刻
            $table->timestamp('left_at')->nullable(); // 退勤時刻
            $table->timestamps(); // created_at, updated_at

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendances');
    }
}
