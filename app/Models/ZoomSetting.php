<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ZoomSetting extends Model
{
    protected $fillable = [
        'school_id',
        'api_key',
        'api_secret',
        'account_id',
        'client_id',
        'client_secret',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'token_expires_at' => 'datetime'
    ];

    protected $hidden = [
        'api_key',
        'api_secret',
        'client_secret',
        'access_token',
        'refresh_token'
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function scopeOwner($query)
    {
        if (Auth::user()) {
            if (Auth::user()->school_id) {
                return $query->where('school_id', Auth::user()->school_id);
            }

            if (!Auth::user()->school_id) {
                if (Auth::user()->hasRole('Super Admin')) {
                    return $query->where('school_id', null);
                }
                if (Auth::user()->hasRole('Guardian')) {
                    $childId = request('child_id');
                    $studentAuth = Students::where('id', $childId)->first();
                    return $query->where('school_id', $studentAuth->school_id);
                }
                return $query->where('school_id', null);
            }
        }

        return $query;
    }
}
