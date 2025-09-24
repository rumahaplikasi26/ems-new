<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceTemp extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'employee_id',
        'machine_id',
        'attendance_method_id',
        'timestamp',
        'site_id',
        'shift_id',
        'longitude',
        'latitude',
        'notes',
        'image_path',
        'image_url',
        'distance',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function attendanceMethod()
    {
        return $this->belongsTo(AttendanceMethod::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

}
