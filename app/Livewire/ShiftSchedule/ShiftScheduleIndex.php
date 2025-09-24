<?php

namespace App\Livewire\ShiftSchedule;

use Livewire\Component;
use App\Livewire\BaseComponent;
use App\Models\EmployeeShiftSchedule;
use App\Models\Employee;
use App\Models\Shift;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ShiftScheduleIndex extends BaseComponent
{
    use LivewireAlert;

    public $search = '';
    public $date_filter = '';
    public $employee_filter = '';
    public $shift_filter = '';

    public function mount()
    {
        $this->date_filter = Carbon::today()->format('Y-m-d');
    }

    public function deleteSchedule($scheduleId)
    {
        try {
            $schedule = EmployeeShiftSchedule::find($scheduleId);
            if ($schedule) {
                $schedule->delete();
                $this->alert('success', 'Shift schedule deleted successfully');
                
                activity()
                    ->causedBy($this->authUser)
                    ->withProperties(['ip' => request()->ip()])
                    ->event('delete')
                    ->log("{$this->authUser->name} deleted shift schedule");
            }
        } catch (\Exception $e) {
            $this->alert('error', 'Error: ' . $e->getMessage());
        }
    }

    public function getSchedulesProperty()
    {
        $query = EmployeeShiftSchedule::with(['employee.user', 'shift']);

        if ($this->search) {
            $query->whereHas('employee.user', function($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->date_filter) {
            $query->whereDate('date', $this->date_filter);
        }

        if ($this->employee_filter) {
            $query->where('employee_id', $this->employee_filter);
        }

        if ($this->shift_filter) {
            $query->where('shift_id', $this->shift_filter);
        }

        return $query->orderBy('date', 'desc')
                    ->orderBy('employee_id')
                    ->paginate(20);
    }

    public function getEmployeesProperty()
    {
        return Employee::with('user')->get();
    }

    public function getShiftsProperty()
    {
        return Shift::where('is_active', true)->get();
    }

    public function render()
    {
        return view('livewire.shift-schedule.shift-schedule-index', [
            'schedules' => $this->schedules,
            'employees' => $this->employees,
            'shifts' => $this->shifts,
        ])->layout('layouts.app', ['title' => 'Shift Schedule Management']);
    }
}
