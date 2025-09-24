<?php

namespace Database\Seeders;

use App\Models\Attendance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all employees
        $employees = \App\Models\Employee::with(['user', 'position.department'])->get();
        
        // Create attendance data for the last 30 days
        $startDate = now()->subDays(30);
        $endDate = now();
        
        foreach ($employees as $employee) {
            // Create attendance for each day
            for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
                // Skip weekends (optional)
                if ($date->isWeekend()) {
                    continue;
                }
                
                // 90% chance of having attendance on a work day
                if (rand(1, 100) <= 90) {
                    $this->createAttendanceForEmployee($employee, $date);
                }
            }
        }
        
        echo "Created attendance data for " . $employees->count() . " employees over 30 days\n";
    }
    
    private function createAttendanceForEmployee($employee, $date)
    {
        // Get employee's shift or default to Shift 1
        $shift = $employee->shift ?? \App\Models\Shift::first();
        
        if (!$shift) {
            return; // No shift available
        }
        
        // Parse shift times
        $startTime = \Carbon\Carbon::parse($shift->start_time);
        $endTime = \Carbon\Carbon::parse($shift->end_time);
        
        // If shift crosses midnight, add a day to end time
        if ($endTime->lt($startTime)) {
            $endTime->addDay();
        }
        
        // Create check-in time (within 1 hour of shift start)
        $checkInTime = $date->copy()
            ->setTime($startTime->hour, $startTime->minute)
            ->addMinutes(rand(-60, 60)); // ±1 hour variation
            
        // Create check-out time (within 1 hour of shift end)
        $checkOutTime = $date->copy()
            ->setTime($endTime->hour, $endTime->minute)
            ->addMinutes(rand(-60, 60)); // ±1 hour variation
            
        // If shift crosses midnight, check-out is next day
        if ($endTime->gt($startTime) && $endTime->hour < 12) {
            $checkOutTime->addDay();
        }
        
        // Get random site and attendance method
        $site = \App\Models\Site::inRandomOrder()->first();
        $attendanceMethod = \App\Models\AttendanceMethod::inRandomOrder()->first();
        
        // Create check-in attendance
        Attendance::factory()->create([
            'employee_id' => $employee->id,
            'timestamp' => $checkInTime,
            'site_id' => $site->id,
            'attendance_method_id' => $attendanceMethod->id,
            'shift_id' => $shift->id,
            'longitude' => $site->longitude + (rand(-100, 100) / 10000), // Small variation
            'latitude' => $site->latitude + (rand(-100, 100) / 10000), // Small variation
        ]);
        
        // 95% chance of having check-out
        if (rand(1, 100) <= 95) {
            Attendance::factory()->create([
                'employee_id' => $employee->id,
                'timestamp' => $checkOutTime,
                'site_id' => $site->id,
                'attendance_method_id' => $attendanceMethod->id,
                'shift_id' => $shift->id,
                'longitude' => $site->longitude + (rand(-100, 100) / 10000),
                'latitude' => $site->latitude + (rand(-100, 100) / 10000),
            ]);
        }
    }
}
