<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StampCorrectionRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_id',
        'target_date',
        'reason',
        'is_approved',
        'corrected_start_time',
        'corrected_end_time',
        'corrected_break_start_time',
        'corrected_break_end_time',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function getStatusLabelAttribute()
    {
        return $this->is_approved ? '承認済み' : '承認待ち';
    }
}
