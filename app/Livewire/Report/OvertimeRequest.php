<?php

namespace App\Livewire\Report;

use App\Models\Employee;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Exports\OvertimeRequestReportExport;
use Maatwebsite\Excel\Facades\Excel;

class OvertimeRequest extends Component
{
    use LivewireAlert;

    public $employees;
    public $selectedEmployees = [];
    public $startDate, $endDate, $countDays;

    public function mount()
    {
        $this->employees = Employee::with('user:id,name')->get(['id', 'user_id']);
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

            $this->dispatch('overtime-request-preview', employees: $this->selectedEmployees, startDate: $this->startDate, endDate: $this->endDate);
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
            $this->dispatch('export-overtime-request-data', employees: $this->selectedEmployees, startDate: $this->startDate, endDate: $this->endDate);
            
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
        }
    }

    public function resetFilter()
    {
        $this->selectedEmployees = [];
        $this->startDate = null;
        $this->endDate = null;
        $this->dispatch('reset-overtime-request-preview');
    }

    public function render()
    {
        return view('livewire.report.overtime-request')->layout('layouts.app', ['title' => 'Report - Overtime Request']);
    }
}
