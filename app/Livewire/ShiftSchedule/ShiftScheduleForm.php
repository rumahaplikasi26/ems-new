<?php

namespace App\Livewire\ShiftSchedule;

use Livewire\Component;
use App\Livewire\BaseComponent;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\EmployeeShiftSchedule;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class ShiftScheduleForm extends BaseComponent
{
    use LivewireAlert;

    public $employee_id;
    public $shift_id;
    public $date;
    public $notes;
    public $is_active = true;
    public $schedule_id;
    public $is_edit = false;

    public $employees;
    public $shifts;

    protected $rules = [
        'employee_id' => 'required|exists:employees,id',
        'shift_id' => 'required|exists:shifts,id',
        'date' => 'required|date',
        'notes' => 'nullable|string|max:255',
        'is_active' => 'boolean',
    ];

    public function mount($scheduleId = null)
    {
        $this->employees = Employee::with('user')->get();
        $this->shifts = Shift::where('is_active', true)->get();
        
        if ($scheduleId) {
            $this->is_edit = true;
            $this->schedule_id = $scheduleId;
            $this->loadSchedule();
        } else {
            $this->date = Carbon::today()->format('Y-m-d');
        }
    }

    public function loadSchedule()
    {
        $schedule = EmployeeShiftSchedule::find($this->schedule_id);
        if ($schedule) {
            $this->employee_id = $schedule->employee_id;
            $this->shift_id = $schedule->shift_id;
            $this->date = $schedule->date->format('Y-m-d');
            $this->notes = $schedule->notes;
            $this->is_active = $schedule->is_active;
        }
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'employee_id' => $this->employee_id,
                'shift_id' => $this->shift_id,
                'date' => $this->date,
                'notes' => $this->notes,
                'is_active' => $this->is_active,
            ];

            if ($this->is_edit) {
                $schedule = EmployeeShiftSchedule::find($this->schedule_id);
                $schedule->update($data);
                $message = 'Shift schedule updated successfully';
            } else {
                EmployeeShiftSchedule::create($data);
                $message = 'Shift schedule created successfully';
            }

            $this->alert('success', $message);
            
            activity()
                ->causedBy($this->authUser)
                ->withProperties(['ip' => request()->ip()])
                ->event($this->is_edit ? 'update' : 'create')
                ->log("{$this->authUser->name} " . ($this->is_edit ? 'updated' : 'created') . " shift schedule");

            return redirect()->route('shift-schedule.index');

        } catch (\Exception $e) {
            $this->alert('error', 'Error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.shift-schedule.shift-schedule-form')
            ->layout('layouts.app', ['title' => $this->is_edit ? 'Edit Shift Schedule' : 'Create Shift Schedule']);
    }
}
