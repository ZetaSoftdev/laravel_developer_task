<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ZoomOnlineClass extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'teacher_id',
        'class_section_id',
        'subject_id',
        'title',
        'description',
        'meeting_id',
        'password',
        'join_url',
        'start_url',
        'start_time',
        'end_time',
        'duration',
        'is_recurring',
        'recurrence_type',
        'recurring_interval',
        'status',
        'session_year_id'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_recurring' => 'boolean',
        'duration' => 'integer',
        'recurring_interval' => 'integer'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function classSection()
    {
        return $this->belongsTo(ClassSection::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function attendances()
    {
        return $this->hasMany(ZoomAttendance::class);
    }

    public function sessionYear()
    {
        return $this->belongsTo(SessionYear::class);
    }
}
