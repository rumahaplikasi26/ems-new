<?php

namespace App\Livewire\Report;

use App\Models\OvertimeRequest;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Carbon as SupportCarbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Exports\OvertimeRequestReportExport;
use Maatwebsite\Excel\Facades\Excel;

class OvertimeRequestPreview extends Component
{
    use LivewireAlert;
    public $startDate;
    public $endDate;
    public $selectedEmployees;
    public $reports;

    #[On('overtime-request-preview')]
    public function preview($employees, $startDate, $endDate)
    {
        $this->selectedEmployees = $employees;

        $this->startDate = Carbon::parse($startDate)->startOfDay();
        $this->endDate = Carbon::parse($endDate)->endOfDay();

        try {
            // Fetch overtime requests
            $overtimeRequests = OvertimeRequest::with('employee.user')
                ->select('id', 'employee_id', 'start_date', 'end_date', 'reason', 'priority', 'is_approved')
                ->whereIn('employee_id', $this->selectedEmployees)
                ->where('is_approved', true)
                ->where(function ($query) {
                    $query->whereBetween('start_date', [$this->startDate, $this->endDate])
                        ->orWhereBetween('end_date', [$this->startDate, $this->endDate])
                        ->orWhere(function ($q) {
                            $q->where('start_date', '<=', $this->startDate)
                              ->where('end_date', '>=', $this->endDate);
                        });
                })
                ->orderBy('start_date')
                ->get();

            $this->reports = $overtimeRequests;

        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
        }
    }

    #[On('reset-overtime-request-preview')]
    public function resetPreview()
    {
        $this->startDate = null;
        $this->endDate = null;
        $this->selectedEmployees = [];
        $this->reports = collect();
    }

    #[On('export-overtime-request-data')]
    public function exportOvertimeRequestData($employees, $startDate, $endDate)
    {
        try {
            // Generate the report data first
            $this->preview($employees, $startDate, $endDate);
            
            if ($this->reports && $this->reports->isNotEmpty()) {
                $fileName = 'overtime_request_report_' . Carbon::parse($startDate)->format('Y-m-d') . '_to_' . Carbon::parse($endDate)->format('Y-m-d') . '.xlsx';
                
                return Excel::download(
                    new OvertimeRequestReportExport($startDate, $endDate, $employees, $this->reports->toArray()),
                    $fileName
                );
            } else {
                $this->alert('warning', 'No data available for export.');
            }
        } catch (\Exception $e) {
            $this->alert('error', 'Export failed: ' . $e->getMessage());
        }
    }

    public function exportExcel()
    {
        try {
            if ($this->reports && $this->reports->isNotEmpty()) {
                $fileName = 'overtime_request_report_' . Carbon::parse($this->startDate)->format('Y-m-d') . '_to_' . Carbon::parse($this->endDate)->format('Y-m-d') . '.xlsx';
                
                return Excel::download(
                    new OvertimeRequestReportExport($this->startDate, $this->endDate, $this->selectedEmployees, $this->reports->toArray()),
                    $fileName
                );
            } else {
                $this->alert('warning', 'No data available for export. Please generate the report first.');
            }
        } catch (\Exception $e) {
            $this->alert('error', 'Export failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.report.overtime-request-preview');
    }
}
