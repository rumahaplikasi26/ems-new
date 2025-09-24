<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class EmployeeShiftSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'shift_id',
        'date',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    // Scope to get active schedules
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope to get schedules for a specific date
    public function scopeForDate($query, $date)
    {
        return $query->where('date', Carbon::parse($date)->format('Y-m-d'));
    }

    // Scope to get schedules for a date range
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [
            Carbon::parse($startDate)->format('Y-m-d'),
            Carbon::parse($endDate)->format('Y-m-d')
        ]);
    }

    // Static method to get employee's shift for a specific date
    public static function getEmployeeShiftForDate($employeeId, $date)
    {
        $schedule = self::active()
            ->where('employee_id', $employeeId)
            ->forDate($date)
            ->with('shift')
            ->first();

        return $schedule?->shift;
    }

    // Static method to get all employees for a shift on a specific date
    public static function getEmployeesForShiftOnDate($shiftId, $date)
    {
        return self::active()
            ->where('shift_id', $shiftId)
            ->forDate($date)
            ->with('employee')
            ->get()
            ->pluck('employee');
    }
}
