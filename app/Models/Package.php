<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;
    protected $connection = 'mysql';

    protected $fillable = [
        'name', 
        'description', 
        'student_charge', 
        'staff_charge', 
        'is_trial', 
        'status', 
        'rank', 
        'duration_type',
        'duration', 
        'days',
        'tag', 
        'highlight', 
        'package_type',
        'type',
        'charges', 
        'no_of_students', 
        'no_of_staffs'
    ];

    protected $casts = [
        'is_trial' => 'boolean',
        'status'   => 'boolean',
        'highlight' => 'boolean',
        'type' => 'integer'
    ];

    protected $appends = ['package_with_type'];

    public function package_feature()
    {
        return $this->hasMany(PackageFeature::class);
    }

    /**
     * Get all of the subscription for the Package
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscription()
    {
        return $this->hasMany(Subscription::class);
    }

    public function getPackageWithTypeAttribute()
    {
        if ($this->type == 1) {
            return $this->name .' #'. trans('postpaid');
        } else {
            return $this->name .' #'. trans('prepaid');
        }
    }

    public function getFinalAmountAttribute()
    {
        if ($this->type == 0) { // Prepaid
            return $this->charges ?? 0;
        } else { // Postpaid
            return 0; // Will be calculated based on usage
        }
    }
}
