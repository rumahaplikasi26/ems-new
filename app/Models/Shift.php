<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    // Helper method to get shift duration in hours
    public function getDurationAttribute()
    {
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        
        // Handle overnight shifts (e.g., 23:00 - 08:00)
        if ($end->lessThan($start)) {
            $end->addDay();
        }
        
        return $start->diffInHours($end);
    }

    // Helper method to check if current time is within shift
    public function isWithinShift($time = null)
    {
        $time = $time ? \Carbon\Carbon::parse($time) : \Carbon\Carbon::now();
        $start = \Carbon\Carbon::parse($this->start_time);
        $end = \Carbon\Carbon::parse($this->end_time);
        
        // Handle overnight shifts
        if ($end->lessThan($start)) {
            $end->addDay();
            if ($time->lessThan($start)) {
                $time->addDay();
            }
        }
        
        return $time->between($start, $end);
    }
}
