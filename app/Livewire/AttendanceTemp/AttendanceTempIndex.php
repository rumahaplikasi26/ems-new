<?php

namespace App\Livewire\AttendanceTemp;

use App\Models\AttendanceTemp;
use App\Livewire\BaseComponent;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceTempIndex extends BaseComponent
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
        $attendances = AttendanceTemp::with('employee.user', 'attendanceMethod', 'shift')
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
        if ($this->authUser->can('view:attendance-all')) {
            $attendances = $attendances->paginate($this->perPage);
        } else {
            // Check if user is a supervisor
            if ($this->authUser->employee && $this->authUser->employee->isSupervisor()) {
                // Get employee IDs under supervision
                $supervisedEmployeeIds = $this->authUser->employee->getSupervisedEmployeeIds();
                // Include supervisor's own attendance
                $supervisedEmployeeIds->push($this->authUser->employee->id);
                $attendances = $attendances->whereIn('employee_id', $supervisedEmployeeIds)->paginate($this->perPage);
            } else {
                // Regular employee - only see their own attendance
                $attendances = $attendances->where('employee_id', $this->authUser->employee->id)->paginate($this->perPage);
            }
        }

        return view('livewire.attendance-temp.attendance-temp-index', compact('attendances'))->layout('layouts.app', ['title' => 'Attendance Temp', 'attendances' => $attendances]);
    }
}
