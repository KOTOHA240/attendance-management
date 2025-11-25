<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'status',
        'started_at',
        'break_started_at',
        'left_at',
        'note',
        'breaks',
        'is_pending',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'breaks' => 'array',
        'date' => 'date',
        'started_at' => 'datetime',
        'break_started_at' => 'datetime',
        'break_ended_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function stampCorrectionRequests()
    {
        return $this->hasMany(StampCorrectionRequest::class, 'attendance_id');
    }

    public function getIsPendingAttribute()
    {
        // 関連する申請を確認
        $request = $this->stampCorrectionRequests()
            ->where('status', '未処理')
            ->latest()
            ->first();

        return $request !== null;
    }
}
