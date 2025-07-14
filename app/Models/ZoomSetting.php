<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
