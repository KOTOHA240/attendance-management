<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStampCorrectionRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stamp_correction_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // 申請者
            $table->string('status')->default('未処理'); // 申請状態（未処理・承認・却下など）
            $table->text('reason')->nullable(); // 修正理由
            $table->timestamp('requested_at')->nullable(); // 申請日時
            $table->timestamps(); // created_at, updated_at

            // 外部キー制約（任意）
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
        Schema::dropIfExists('stamp_correction_requests');
    }
}
