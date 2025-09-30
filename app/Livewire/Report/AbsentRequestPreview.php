<?php

namespace App\Livewire\Report;

use App\Models\AbsentRequest;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use DatePeriod;
use Illuminate\Support\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Exports\AbsentRequestReportExport;
use Maatwebsite\Excel\Facades\Excel;

class AbsentRequestPreview extends Component
{
    use LivewireAlert;
    public $startDate;
    public $endDate;
    public $selectedEmployees;
    public $reports;

    #[On('absent-request-preview')]
    public function preview($employees, $startDate, $endDate)
    {
        $this->selectedEmployees = $employees;

        $this->startDate = Carbon::parse($startDate)->startOfDay();
        $this->endDate = Carbon::parse($endDate)->endOfDay();

        // dd($this->startDate, $this->endDate);
        $this->countDays = daysBetween($this->startDate, $this->endDate) + 1;

        try {
            // Fetch absent requests
            $absentRequests = AbsentRequest::with('employee.user')->select('employee_id', 'start_date', 'end_date', 'type_absent', 'notes')
                ->whereIn('employee_id', $this->selectedEmployees)
                ->where('is_approved', true)
                ->where(function ($query){
                    $query->whereBetween('start_date', [$this->startDate, $this->endDate])
                        ->orWhereBetween('end_date', [$this->startDate, $this->endDate]);
                })
                ->get();

            $this->reports = $absentRequests;
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
        }
    }

    #[On('reset-absent-request-preview')]
    public function resetPreview()
    {
        $this->startDate = null;
        $this->endDate = null;
        $this->selectedEmployees = [];
        $this->reports = collect();
    }

    #[On('export-absent-request-data')]
    public function exportAbsentRequestData($employees, $startDate, $endDate)
    {
        try {
            // Generate the report data first
            $this->preview($employees, $startDate, $endDate);
            
            if ($this->reports && $this->reports->isNotEmpty()) {
                $fileName = 'absent_request_report_' . Carbon::parse($startDate)->format('Y-m-d') . '_to_' . Carbon::parse($endDate)->format('Y-m-d') . '.xlsx';
                
                // Log for debugging
                \Log::info('Exporting absent request data', [
                    'count' => $this->reports->count(),
                    'first_item' => $this->reports->first(),
                    'employees' => $employees,
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]);
                
                return Excel::download(
                    new AbsentRequestReportExport($startDate, $endDate, $employees, $this->reports),
                    $fileName
                );
            } else {
                $this->alert('warning', 'No data available for export.');
            }
        } catch (\Exception $e) {
            \Log::error('Export failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->alert('error', 'Export failed: ' . $e->getMessage());
        }
    }

    public function exportExcel()
    {
        try {
            if ($this->reports && $this->reports->isNotEmpty()) {
                $fileName = 'absent_request_report_' . Carbon::parse($this->startDate)->format('Y-m-d') . '_to_' . Carbon::parse($this->endDate)->format('Y-m-d') . '.xlsx';
                
                // Log for debugging
                \Log::info('Exporting absent request data (exportExcel)', [
                    'count' => $this->reports->count(),
                    'first_item' => $this->reports->first(),
                    'employees' => $this->selectedEmployees,
                    'start_date' => $this->startDate,
                    'end_date' => $this->endDate
                ]);
                
                return Excel::download(
                    new AbsentRequestReportExport($this->startDate, $this->endDate, $this->selectedEmployees, $this->reports),
                    $fileName
                );
            } else {
                $this->alert('warning', 'No data available for export. Please generate the report first.');
            }
        } catch (\Exception $e) {
            \Log::error('Export failed (exportExcel)', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->alert('error', 'Export failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.report.absent-request-preview');
    }
}
