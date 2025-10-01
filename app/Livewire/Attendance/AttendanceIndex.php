<?php

namespace App\Livewire\Attendance;

use App\Models\Attendance;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Livewire\Component;

class AttendanceIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $start_date = '';
    public $end_date = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'start_date' => ['except' => ''],
        'end_date' => ['except' => ''],
        'employee_id' => ['except' => ''],
    ];

    protected $listeners = ['refresh' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStart_date()
    {
        $this->resetPage();
    }

    public function updatingEnd_date()
    {
        $this->resetPage();
    }

    public function resetFilter()
    {
        $this->reset(['search', 'start_date', 'end_date']);
    }

    public function render()
    {
        $attendances = Attendance::with('employee.user', 'attendanceMethod', 'shift', 'site')
        ->when($this->search, function ($query) {
            $query->whereHas('employee.user', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            });
        })->when($this->start_date, function ($query) {
            $query->whereDate('timestamp', '>=', $this->start_date);
        })->when($this->end_date, function ($query) {
            $query->whereDate('timestamp', '<=', $this->end_date);
        })->orderBy('timestamp', 'desc');

        // Apply supervisor filter
        $authUser = Auth::user();
        if ($authUser->can('view:attendance-all')) {
            $attendances = $attendances->paginate($this->perPage);
        } else {
            // Check if user is a supervisor
            if ($authUser->employee && $authUser->employee->isSupervisor()) {
                // Get employee IDs under supervision
                $supervisedEmployeeIds = $authUser->employee->getSupervisedEmployeeIds();
                // Include supervisor's own attendance
                $supervisedEmployeeIds->push($authUser->employee->id);
                $attendances = $attendances->whereIn('employee_id', $supervisedEmployeeIds)->paginate($this->perPage);
            } else {
                // Regular employee - only see their own attendance
                $attendances = $attendances->where('employee_id', $authUser->employee->id)->paginate($this->perPage);
            }
        }

        return view('livewire.attendance.attendance-index', compact('attendances'))->layout('layouts.app', ['title' => 'Attendance']);
    }
}
