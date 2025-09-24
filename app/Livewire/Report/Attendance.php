<?php

namespace App\Livewire\Report;

use App\Models\Employee;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use DatePeriod;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Attendance as AttendanceModel;
use App\Exports\AttendanceReportExport;
use Maatwebsite\Excel\Facades\Excel;

class Attendance extends Component
{
    use LivewireAlert;

    public $employees;
    public $selectedEmployees = [];
    public $startDate, $endDate, $countDays;
    public $shifts;
    public $selectedShifts = [];

    public function mount()
    {
        $this->employees = Employee::with('user:id,name')->get(['id', 'user_id']);
        $this->shifts = \App\Models\Shift::where('is_active', true)->get(['id', 'name']);
    }

    #[On('change-input-form')]
    public function changeInputForm($param, $value)
    {
        $this->$param = $value;
    }

    public function preview()
    {
        try {
            $this->validate([
                'startDate' => 'required',
                'endDate' => 'required',
                'selectedEmployees' => 'required|array|min:1'
            ]);

            $this->dispatch('attendance-preview', employees: $this->selectedEmployees, startDate: $this->startDate, endDate: $this->endDate, shifts: $this->selectedShifts);
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
        }
    }

    public function exportExcel()
    {
        try {
            $this->validate([
                'startDate' => 'required',
                'endDate' => 'required',
                'selectedEmployees' => 'required|array|min:1'
            ]);

            // Get the reports data by dispatching to preview component
            $this->dispatch('export-attendance-data', employees: $this->selectedEmployees, startDate: $this->startDate, endDate: $this->endDate, shifts: $this->selectedShifts);
            
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
        }
    }

    public function resetFilter()
    {
        $this->selectedEmployees = [];
        $this->selectedShifts = [];
        $this->startDate = null;
        $this->endDate = null;
        $this->dispatch('reset-attendance-preview');
    }

    public function render()
    {
        return view('livewire.report.attendance')->layout('layouts.app', ['title' => 'Report - Attendance']);
    }
}
