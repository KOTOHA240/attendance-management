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
        'status',
        'corrected_start_time',
        'corrected_end_time',
        'corrected_breaks',
        'note',
    ];

    protected $casts = [
        'corrected_breaks' => 'array',
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
        switch ($this->status) {
            case 'pending':
                return '承認待ち';
            case 'approved':
                return '承認済み';
            default:
                return '未処理';
        }
    }
}
