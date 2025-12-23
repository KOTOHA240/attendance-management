<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'status',
        'started_at',
        'break_started_at',
        'break_ended_at',
        'left_at',
        'note',
        'breaks',
        'is_pending',
    ];

    protected $casts = [
        'breaks' => 'array',
        'started_at' => 'datetime',
        'break_started_at' => 'datetime',
        'break_ended_at' => 'datetime',
        'left_at' => 'datetime',
        'is_pending' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stampCorrectionRequests()
    {
        return $this->hasMany(StampCorrectionRequest::class);
    }

    public function getBreakTimeAttribute()
    {
        $total = 0;
        if (is_array($this->breaks)) {
            foreach ($this->breaks as $break) {
                if (!empty($break['start']) && !empty($break['end'])) {
                    $start = Carbon::parse($break['start']);
                    $end   = Carbon::parse($break['end']);
                    $total += $end->diffInMinutes($start);
                }
            }
        }
        return $total > 0 ? sprintf('%02d:%02d', intdiv($total,60), $total%60) : null;
    }

    public function getWorkTimeAttribute()
    {
        if ($this->started_at && $this->left_at) {
            $total = $this->left_at->diffInMinutes($this->started_at);
            $break = 0;
            if (is_array($this->breaks)) {
                foreach ($this->breaks as $b) {
                    if (!empty($b['start']) && !empty($b['end'])) {
                        $break += Carbon::parse($b['end'])->diffInMinutes(Carbon::parse($b['start']));
                    }
                }
            }
            $work = $total - $break;
            return $work > 0 ? sprintf('%02d:%02d', intdiv($work,60), $work%60) : null;
        }
        return null;
    }

    public function scopeApprovedOrNormal($query)
    {
        return $query->where(function ($q) {
            $q->whereDoesntHave('stampCorrectionRequests') // 申請がない（通常打刻）
                ->orWhereHas('stampCorrectionRequests', function ($sub) {
                  $sub->where('status', 'approved'); // 承認済み申請がある
                });
        });
    }
}
