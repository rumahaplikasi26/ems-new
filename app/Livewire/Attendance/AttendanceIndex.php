<?php

namespace App\Livewire\Attendance;

use App\Livewire\BaseComponent;
use App\Models\Attendance;
use App\Helpers\ShiftHelper;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class AttendanceIndex extends BaseComponent
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
        // Build base query with filters - more robust for server deployment
        $baseQuery = Attendance::with(['employee.user', 'machine', 'site', 'attendanceMethod', 'shift'])
            ->when($this->search, function ($query) {
                $searchTerm = trim($this->search);
                $query->whereHas('employee.user', function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%');
                });
            })
            ->when($this->start_date, function ($query) {
                $startDate = Carbon::parse($this->start_date)->format('Y-m-d');
                $query->whereDate('timestamp', '>=', $startDate);
            })
            ->when($this->end_date, function ($query) {
                $endDate = Carbon::parse($this->end_date)->format('Y-m-d');
                $query->whereDate('timestamp', '<=', $endDate);
            })->orderBy('timestamp', 'desc');

        // Apply user permissions to the query
        if ($this->authUser->can('view:attendance-all')) {
            $attendances = $baseQuery->paginate($this->perPage);
        } else {
            // Check if user is a supervisor
            if ($this->authUser->employee && $this->authUser->employee->isSupervisor()) {
                // Get employee IDs under supervision
                $supervisedEmployeeIds = $this->authUser->employee->getSupervisedEmployeeIds();
                // Include supervisor's own attendance
                $supervisedEmployeeIds->push($this->authUser->employee->id);
                $attendances = $baseQuery->whereIn('employee_id', $supervisedEmployeeIds)->paginate($this->perPage);
            } else {
                // Regular employee - only see their own attendance
                $attendances = $baseQuery->where('employee_id', $this->authUser->employee->id)->paginate($this->perPage);
            }
        }

        return view('livewire.attendance.attendance-index', compact('attendances'))->layout('layouts.app', ['title' => 'Attendance List']);
    }

}
