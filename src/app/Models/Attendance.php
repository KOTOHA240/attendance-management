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
}
