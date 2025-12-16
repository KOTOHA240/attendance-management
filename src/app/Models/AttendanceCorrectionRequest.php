<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectionRequest extends Model
{
    protected $fillable = [
        'user_id', 'attendance_id', 'target_date', 'reason', 'status',
    ];

    protected $casts = [
        'target_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // リレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->attributes['status']) {
            'pending'  => '承認待ち',
            'approved' => '承認済み',
            'rejected' => '却下',
            default    => $this->attributes['status'],
        };
    }
}

