<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run()
    {
        // 一般ユーザーを取得
        $user = User::where('email', 'user@example.com')->first();

        if (!$user) {
            $this->command->error('一般ユーザーが見つかりません。先に UserSeeder を実行してください。');
            return;
        }

        // 1ヶ月分の勤怠データ（例：2025年1月）
        $startDate = Carbon::create(2025, 1, 1);
        $endDate = Carbon::create(2025, 1, 31);

        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {

            // 土日を欠勤扱いにしたい場合はここでスキップ
            // if ($date->isWeekend()) continue;

            // 30% の確率で欠勤
            $isAbsent = rand(1, 100) <= 30;

            if ($isAbsent) {
                Attendance::create([
                    'user_id' => $user->id,
                    'status' => '欠勤',
                    'started_at' => null,
                    'break_started_at' => null,
                    'left_at' => null,
                    'break_time' => 0,
                    'work_time' => 0,
                    'note' => '欠勤',
                    'breaks' => json_encode([]),
                    'is_pending' => false,
                ]);
                continue;
            }

            // 出勤日のデータ
            Attendance::create([
                'user_id' => $user->id,
                'status' => '勤務済み',
                'started_at' => $date->copy()->setTime(9, 0),
                'break_started_at' => $date->copy()->setTime(12, 30),
                'left_at' => $date->copy()->setTime(18, 0),
                'break_time' => 60,
                'work_time' => 8 * 60,
                'note' => null,
                'breaks' => json_encode([
                    [
                        'start' => '12:30',
                        'end' => '13:30'
                    ]
                ]),
                'is_pending' => false,
            ]);
        }
    }
}

