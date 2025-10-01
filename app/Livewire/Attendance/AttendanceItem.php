<?php

namespace App\Livewire\Attendance;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class AttendanceItem extends Component
{
    #[Reactive]
    public $attendance;
    public $day;
    public $employee;
    public $checkIn;
    public $checkOut;
    public $duration;
    public $duration_string;
    public $badge_color;
    public $distanceInFormatted, $distanceOutFormatted;
    public $noteInExcerpt, $noteOutExcerpt;
    public $approvedByIn, $approvedAtIn, $approvedByOut, $approvedAtOut;
    public $date;
    public $shift_date;
    public $status;
    public $time_range;
    public $image_url;
    public $employee_name;
    public $employee_email;
    /**
     * Mount the component
     *
     * This method is called when the component is first initialized.
     * It sets the following properties:
     * - $this->employee
     * - $this->day
     * - $this->checkIn
     * - $this->checkOut
     * - $this->duration_string
     * - $this->duration
     * - $this->badge_color
     * - $this->distanceInFormatted
     * - $this->distanceOutFormatted
     * - $this->noteInExcerpt
     * - $this->noteOutExcerpt
     */
    public function mount()
    {
        $this->employee = $this->attendance['employee'];
        $this->day = date('d/m', strtotime($this->attendance['date']));
        $this->checkIn = $this->attendance['check_in'];
        $this->checkOut = $this->attendance['check_out'];
        $this->duration_string = $this->attendance['duration_string'];
        $this->duration = $this->attendance['duration'];
        $this->date = $this->attendance['date'] ?? '';
        $this->shift_date = $this->attendance['shift_date'] ?? '';
        $this->status = $this->attendance['status'] ?? '';
        $this->time_range = $this->attendance['time_range'] ?? '';
        $this->badge_color = $this->statusDuration();
        $this->image_url = $this->attendance['image_url'] ?? '';
        $this->distanceFormatted();
        $this->notesExcerpt();
    }

    public function notesExcerpt()
    {
        // Handle check-in notes
        if ($this->checkIn && isset($this->checkIn['notes'])) {
            if (strlen($this->checkIn['notes']) > 50) {
                $excerptIn = substr($this->checkIn['notes'], 0, 50);
                $this->noteInExcerpt = '<p class="d-inline-block m-0 fst-italic" id="notes-in-' . $this->checkIn['id'] . '">' . $excerptIn . '</p> <a data-id="' . $this->checkIn['id'] . '" data-excerpt="' . $excerptIn . '" data-notes="' . $this->checkIn['notes'] . '" href="javascript: void(0);" class="read-more-in">Read More</a>';
            } else {
                $this->noteInExcerpt = '<p class="fst-italic">' . $this->checkIn['notes'] . '</p>';
            }
        } else {
            $this->noteInExcerpt = '<p class="fst-italic">-</p>';
        }

        // Handle check-out notes
        if ($this->checkOut && isset($this->checkOut['notes'])) {
            if (strlen($this->checkOut['notes']) > 50) {
                $excerptOut = substr($this->checkOut['notes'], 0, 50);
                $this->noteOutExcerpt = '<p class="d-inline-block m-0 fst-italic" id="notes-out-' . $this->checkOut['id'] . '">' . $excerptOut . '</p> <a data-id="' . $this->checkOut['id'] . '" data-excerpt="' . $excerptOut . '" data-notes="' . $this->checkOut['notes'] . '" href="javascript: void(0);" class="read-more-out">Read More</a>';
            } else {
                $this->noteOutExcerpt = '<p class="fst-italic">' . $this->checkOut['notes'] . '</p>';
            }
        } else {
            $this->noteOutExcerpt = '<p class="fst-italic">-</p>';
        }
    }

    public function distanceFormatted()
    {
        // Handle check-in distance
        if ($this->checkIn && isset($this->checkIn['distance']) && $this->checkIn['distance'] != null) {
            if ($this->checkIn['distance'] < 1) {
                $this->distanceInFormatted = '<span class="text-success">' . $this->checkIn['distance'] . ' Km</span>';
            } elseif ($this->checkIn['distance'] >= 1) {
                $this->distanceInFormatted = '<span class="text-warning">' . $this->checkIn['distance'] . ' Km</span>';
            } else {
                $this->distanceInFormatted = '<span class="text-danger">' . $this->checkIn['distance'] . ' Km</span>';
            }
        } else {
            $this->distanceInFormatted = '<span class="text-secondary">Tidak ada</span>';
        }

        // Handle check-out distance
        if ($this->checkOut && isset($this->checkOut['distance']) && $this->checkOut['distance'] != null) {
            if ($this->checkOut['distance'] < 1) {
                $this->distanceOutFormatted = '<span class="text-success">' . $this->checkOut['distance'] . ' Km</span>';
            } elseif ($this->checkOut['distance'] >= 1) {
                $this->distanceOutFormatted = '<span class="text-warning">' . $this->checkOut['distance'] . ' Km</span>';
            } else {
                $this->distanceOutFormatted = '<span class="text-danger">' . $this->checkOut['distance'] . ' Km</span>';
            }
        } else {
            $this->distanceOutFormatted = '<span class="text-secondary">Tidak ada</span>';
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
        return view('livewire.attendance.attendance-item');
    }
}
