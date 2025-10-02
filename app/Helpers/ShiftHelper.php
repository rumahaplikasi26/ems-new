<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\Shift;

class ShiftHelper
{
    /**
     * Get the correct date for attendance based on shift type
     * For overnight shifts, check-out should be attributed to the previous day
     */
    public static function getAttendanceDate($timestamp, $shift)
    {
        $time = Carbon::parse($timestamp);
        
        if (!$shift) {
            return $time->format('Y-m-d');
        }
        
        $shiftStart = Carbon::parse($shift->start_time);
        $shiftEnd = Carbon::parse($shift->end_time);
        
        // Check if this is an overnight shift
        if ($shiftEnd->lessThan($shiftStart)) {
            // Overnight shift (e.g., 23:00-08:00 or 15:00-00:00)
            
            // If time is before midnight but after shift start, it's the same day
            if ($time->hour >= $shiftStart->hour) {
                return $time->format('Y-m-d');
            }
            
            // If time is after midnight but before shift end, it's the previous day
            if ($time->hour < $shiftEnd->hour) {
                return $time->subDay()->format('Y-m-d');
            }
            
            // For overnight shifts, if time doesn't fit either condition,
            // determine which day it belongs to based on proximity to shift times
            $hoursFromShiftStart = $time->hour - $shiftStart->hour;
            $hoursFromShiftEnd = $time->hour - $shiftEnd->hour;
            
            // If closer to shift start, it's the same day
            if (abs($hoursFromShiftStart) <= abs($hoursFromShiftEnd)) {
                return $time->format('Y-m-d');
            } else {
                // If closer to shift end, it's the previous day
                return $time->subDay()->format('Y-m-d');
            }
        }
        
        // Regular shift - use the actual date
        return $time->format('Y-m-d');
    }
    
    /**
     * Group attendance records by shift-aware date
     * This handles overnight shifts correctly
     */
    public static function groupAttendanceByShiftDate($attendances)
    {
        $grouped = collect();
        
        foreach ($attendances as $attendance) {
            $attendanceDate = self::getAttendanceDate($attendance->timestamp, $attendance->shift);
            
            if (!$grouped->has($attendanceDate)) {
                $grouped[$attendanceDate] = collect();
            }
            
            $grouped[$attendanceDate]->push($attendance);
        }
        
        return $grouped;
    }
    
    /**
     * Get check-in and check-out for a specific date considering overnight shifts
     */
    public static function getCheckInOutForDate($attendances, $date, $shift)
    {
        $dateAttendances = $attendances->filter(function ($attendance) use ($date, $shift) {
            $attendanceDate = self::getAttendanceDate($attendance->timestamp, $shift);
            return $attendanceDate === $date;
        });
        
        if ($dateAttendances->isEmpty()) {
            return [null, null];
        }
        
        $sorted = $dateAttendances->sortBy('timestamp');
        $checkIn = $sorted->first();
        $checkOut = $sorted->last();
        
        // For overnight shifts, we need to handle the case where check-out is on the next day
        if ($shift && self::isOvernightShift($shift)) {
            // If check-in and check-out are on different calendar days, 
            // we need to find the actual check-out for this shift
            $checkInDate = Carbon::parse($checkIn->timestamp)->format('Y-m-d');
            $checkOutDate = Carbon::parse($checkOut->timestamp)->format('Y-m-d');
            
            if ($checkInDate !== $checkOutDate) {
                // Check-out is on the next day, which is correct for overnight shifts
                return [$checkIn, $checkOut];
            }
        }
        
        return [$checkIn, $checkOut];
    }
    
    /**
     * Check if a shift is an overnight shift
     */
    public static function isOvernightShift($shift)
    {
        if (!$shift) {
            return false;
        }
        
        $start = Carbon::parse($shift->start_time);
        $end = Carbon::parse($shift->end_time);
        
        return $end->lessThan($start);
    }
    
    /**
     * Get shift display name with time range
     */
    public static function getShiftDisplayName($shift)
    {
        if (!$shift) {
            return '';
        }
        
        $start = Carbon::parse($shift->start_time)->format('H:i');
        $end = Carbon::parse($shift->end_time)->format('H:i');
        
        if (self::isOvernightShift($shift)) {
            return "{$shift->name} ({$start}-{$end}+1)";
        }
        
        return "{$shift->name} ({$start}-{$end})";
    }
    
    /**
     * Format attendance time range considering overnight shifts
     */
    public static function formatAttendanceTimeRange($checkIn, $checkOut, $shift = null)
    {
        if (!$checkIn || !$checkOut) {
            return '-';
        }
        
        $checkInTime = Carbon::parse($checkIn->timestamp)->format('H:i');
        $checkOutTime = Carbon::parse($checkOut->timestamp)->format('H:i');
        
        $timeRange = "{$checkInTime}-{$checkOutTime}";
        
        // Add shift info if available
        if ($shift) {
            $timeRange .= ' (' . $shift->name . ')';
            
            // Add indicator for overnight shifts
            if (self::isOvernightShift($shift)) {
                $checkInDate = Carbon::parse($checkIn->timestamp)->format('Y-m-d');
                $checkOutDate = Carbon::parse($checkOut->timestamp)->format('Y-m-d');
                
                if ($checkInDate !== $checkOutDate) {
                    $timeRange .= ' [Overnight]';
                }
            }
        }
        
        return $timeRange;
    }
    
    /**
     * Get attendance status considering overnight shifts
     */
    public static function getAttendanceStatus($checkIn, $checkOut, $shift = null)
    {
        if (!$checkIn && !$checkOut) {
            return 'A'; // Absent
        }
        
        if ($checkIn && !$checkOut) {
            return 'P'; // Present (check-in only)
        }
        
        if ($checkIn && $checkOut) {
            // Calculate duration
            $duration = Carbon::parse($checkIn->timestamp)->diffInMinutes(Carbon::parse($checkOut->timestamp));
            $hours = floor($duration / 60);
            $minutes = $duration % 60;
            
            $status = "P {$hours}h {$minutes}m";
            
            // Add shift info if available
            if ($shift) {
                $status .= " ({$shift->name})";
            }
            
            return $status;
        }
        
        return 'A'; // Absent
    }
    
    /**
     * Get all dates that should be considered for attendance reporting
     * This includes handling overnight shifts where attendance spans multiple calendar days
     */
    public static function getAttendanceDates($startDate, $endDate, $shift = null)
    {
        $dates = collect();
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        while ($current->lte($end)) {
            $dates->push($current->format('Y-m-d'));
            $current->addDay();
        }
        
        // For overnight shifts, we might need to include the previous day
        if ($shift && self::isOvernightShift($shift)) {
            $previousDay = Carbon::parse($startDate)->subDay()->format('Y-m-d');
            if (!$dates->contains($previousDay)) {
                $dates->prepend($previousDay);
            }
        }
        
        return $dates;
    }
}
