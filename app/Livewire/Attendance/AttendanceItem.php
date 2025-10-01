<?php

namespace App\Livewire\Attendance;

use App\Models\Attendance;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class AttendanceItem extends Component
{
    #[Reactive]
    public $attendance;
    
    public function mount(Attendance $attendance)
    {
        $this->attendance = $attendance;
    }

    public function approvedInfo()
    {
        // Handle approval information from Eloquent model with safe checks
        if ($this->attendance && isset($this->attendance->approvedBy) && $this->attendance->approvedBy) {
            return $this->attendance->approvedBy->name ?? 'Unknown';
        } else {
            return '-';
        }
    }

    public function approvedAtInfo()
    {
        // Handle approval timestamp from Eloquent model with safe checks
        if ($this->attendance && isset($this->attendance->approved_at) && $this->attendance->approved_at) {
            try {
                return $this->attendance->approved_at->format('d-m-Y H:i:s');
            } catch (\Exception $e) {
                return $this->attendance->approved_at;
            }
        } else {
            return '-';
        }
    }

    public function notesExcerpt()
    {
        // Handle attendance notes from Eloquent model with safe checks
        if ($this->attendance && isset($this->attendance->notes) && !empty($this->attendance->notes)) {
            if (strlen($this->attendance->notes) > 50) {
                $excerpt = substr($this->attendance->notes, 0, 50);
                return '<p class="d-inline-block m-0 fst-italic" id="notes-' . $this->attendance->id . '">' . $excerpt . '</p> <a data-id="' . $this->attendance->id . '" data-excerpt="' . $excerpt . '" data-notes="' . $this->attendance->notes . '" href="javascript: void(0);" class="read-more">Read More</a>';
            } else {
                return '<p class="fst-italic">' . $this->attendance->notes . '</p>';
            }
        } else {
            return '<p class="fst-italic">-</p>';
        }
    }

    public function distanceFormatted()
    {
        // Handle attendance distance from Eloquent model with safe checks
        if ($this->attendance && isset($this->attendance->distance) && $this->attendance->distance !== null) {
            if ($this->attendance->distance < 1) {
                return '<span class="text-success">' . $this->attendance->distance . ' Km</span>';
            } elseif ($this->attendance->distance >= 1) {
                return '<span class="text-warning">' . $this->attendance->distance . ' Km</span>';
            } else {
                return '<span class="text-danger">' . $this->attendance->distance . ' Km</span>';
            }
        } else {
            return '<span class="text-secondary">Tidak ada</span>';
        }
    }

    public function statusDuration()
    {
        if ($this->duration < 8.00) {
            return 'badge-soft-danger';
        } else {
            return 'badge-soft-success';
        }
    }

    public function render()
    {
        // Safe data extraction with null checks
        $day = $this->attendance->timestamp ? date('d/m', strtotime($this->attendance->timestamp)) : 'N/A';
        $employee_name = $this->attendance->employee->user->name ?? 'Unknown';
        $employee_email = $this->attendance->employee->user->email ?? 'No email';
        $image_url = $this->attendance->image_url ?? null;
        $shift_name = $this->attendance->shift->name ?? 'Unknown';
        
        // Safe shift date calculation
        $shift_date = null;
        if ($this->attendance->timestamp && $this->attendance->shift) {
            try {
                $shift_date = \App\Helpers\ShiftHelper::getAttendanceDate($this->attendance->timestamp, $this->attendance->shift);
            } catch (\Exception $e) {
                $shift_date = date('Y-m-d', strtotime($this->attendance->timestamp));
            }
        }
        
        $date = $this->attendance->timestamp ? date('d-m-Y', strtotime($this->attendance->timestamp)) : 'N/A';
        $site_name = $this->attendance->site->name ?? 'Unknown';
        $latitude = $this->attendance->latitude ?? 'Unknown';
        $longitude = $this->attendance->longitude ?? 'Unknown';
        $timestamp = $this->attendance->timestamp ?? 'Unknown';
        $time_formatted = $this->attendance->timestamp ? date('H:i:s', strtotime($this->attendance->timestamp)) : 'N/A';
        $approvedBy = $this->approvedInfo();
        $approvedAt = $this->approvedAtInfo();
        $distanceFormatted = $this->distanceFormatted();
        $noteExcerpt = $this->notesExcerpt();

        return view('livewire.attendance.attendance-item', [
            'attendance' => $this->attendance,
            'employee_name' => $employee_name,
            'employee_email' => $employee_email,
            'image_url' => $image_url,
            'day' => $day,
            'date' => $date,
            'shift_date' => $shift_date,
            'time_formatted' => $time_formatted,
            'site_name' => $site_name,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'timestamp' => $timestamp,
            'approvedBy' => $approvedBy,
            'approvedAt' => $approvedAt,
            'shift_name' => $shift_name,
            'distanceFormatted' => $distanceFormatted,
            'noteExcerpt' => $noteExcerpt,
        ]);
    }
}
