<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZoomAttendance extends Model
{
    protected $fillable = [
        'zoom_online_class_id',
        'student_id',
        'join_time',
        'leave_time',
        'duration',
        'status',
        'remarks'
    ];

    protected $casts = [
        'join_time' => 'datetime',
        'leave_time' => 'datetime',
        'duration' => 'integer'
    ];

    public function onlineClass()
    {
        return $this->belongsTo(ZoomOnlineClass::class, 'zoom_online_class_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
} 