<?php

namespace App\Livewire\DailyReport;

use Livewire\Component;
use App\Models\Employee;
use App\Models\DailyReport;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class DailyReportAll extends Component
{
    use WithPagination;     
    public $employee_id = [];
    public $perPage = 10;
    public $start_date;
    public $end_date;

    #[Url(except: '')]
    public $search = '';

    public $employees;

    public function mount()
    {
        $this->employees = Employee::with('user')->get();
    }

    public function resetFilter()
    {
        $this->search = '';
        $this->employee_id = [];
        $this->start_date = '';
        $this->end_date = '';
        
        // Reset select2
        $this->dispatch('resetSelect2');
        
        // Reset date picker
        $this->dispatch('resetDatePicker');
    }

    public function render()
    {
        $query = DailyReport::with('employee.user', 'dailyReportRecipients.employee.user');

        // Search filter
        if (!empty($this->search)) {
            $query->where('description', 'like', '%' . $this->search . '%');
        }

        // Employee filter
        if (!empty($this->employee_id)) {
            $query->whereIn('employee_id', $this->employee_id);
        }

        // Date filters
        if (!empty($this->start_date)) {
            $query->whereDate('date', '>=', $this->start_date);
        }

        if (!empty($this->end_date)) {
            $query->whereDate('date', '<=', $this->end_date);
        }

        $daily_reports = $query->latest()->paginate($this->perPage);

        return view('livewire.daily-report.daily-report-all', compact('daily_reports'))->layout('layouts.app', ['title' => 'Daily Report All']);
    }
}
