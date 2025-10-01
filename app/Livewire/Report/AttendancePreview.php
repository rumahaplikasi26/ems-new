<?php

namespace App\Livewire\Report;

use App\Models\AbsentRequest;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Helpers\ShiftHelper;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
use DatePeriod;
use Illuminate\Support\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Exports\AttendanceReportExport;
use Maatwebsite\Excel\Facades\Excel;

class AttendancePreview extends Component
{
    use LivewireAlert;
    public $startDate;
    public $endDate;
    public $selectedEmployees;
    public $selectedShifts = [];
    public $countDays;
    public $reports;

    #[On('attendance-preview')]
    public function preview($employees, $startDate, $endDate, $shifts = [])
    {
        $this->selectedEmployees = $employees;
        $this->selectedShifts = $shifts;

        $this->startDate = Carbon::parse($startDate)->startOfDay();
        $this->endDate = Carbon::parse($endDate)->endOfDay();

        // dd($this->startDate, $this->endDate);
        $this->countDays = daysBetween($this->startDate, $this->endDate) + 1;

        try {

            // dd($this->startDate, $this->endDate, $this->startDate, $this->endDate);
            // Eager load semua data yang diperlukan dalam satu query
            $employees = Employee::with(['user:id,name'])
                ->whereIn('id', $this->selectedEmployees)
                ->get(['id', 'user_id']);

            // Fetch semua attendance data dalam satu query dengan shift information
            $attendanceQuery = Attendance::with('shift:id,name,start_time,end_time')
                ->select('employee_id', 'timestamp', 'shift_id')
                ->whereIn('employee_id', $this->selectedEmployees)
                ->whereBetween('timestamp', [$this->startDate, $this->endDate]);
            
            // Filter by shifts if selected
            if (!empty($this->selectedShifts)) {
                $attendanceQuery->whereIn('shift_id', $this->selectedShifts);
            }
            
            $attendances = $attendanceQuery->orderBy('timestamp')->get();

            // Fetch leave requests
            $leaveRequests = LeaveRequest::select('employee_id', 'start_date', 'end_date')
                ->whereIn('employee_id', $this->selectedEmployees)
                ->where('is_approved', true)
                ->where(function ($query) {
                    $query->whereBetween('start_date', [$this->startDate, $this->endDate])
                        ->orWhereBetween('end_date', [$this->startDate, $this->endDate]);
                })
                ->get();

            // Fetch absent requests
            $absentRequests = AbsentRequest::select('employee_id', 'start_date', 'end_date', 'type_absent')
                ->whereIn('employee_id', $this->selectedEmployees)
                ->where('is_approved', true)
                ->where(function ($query){
                    $query->whereBetween('start_date', [$this->startDate, $this->endDate])
                        ->orWhereBetween('end_date', [$this->startDate, $this->endDate]);
                })
                ->get();

            $dateRange = new DatePeriod(
                Carbon::parse($this->startDate),
                CarbonInterval::day(),
                Carbon::parse($this->endDate)
            );

            // dd($dateRange);
            $this->reports = collect();

            foreach ($employees as $employee) {
                // Get employee attendances and group by shift-aware date
                $employeeAttendances = $attendances->where('employee_id', $employee->id);
                $groupedByShiftDate = ShiftHelper::groupAttendanceByShiftDate($employeeAttendances);

                // Buat array untuk menyimpan periode cuti
                $leaveDates = [];
                foreach ($leaveRequests->where('employee_id', $employee->id) as $leave) {
                    $period = CarbonPeriod::create($leave->start_date, $leave->end_date);
                    foreach ($period as $date) {
                        $leaveDates[$date->format('Y-m-d')] = 'L';
                    }
                }

                // Buat array untuk menyimpan periode absent dengan type
                $absentDates = [];
                foreach ($absentRequests->where('employee_id', $employee->id) as $absent) {
                    $period = CarbonPeriod::create($absent->start_date, $absent->end_date);
                    $absentType = $this->getAbsentTypeCode($absent->type_absent);
                    foreach ($period as $date) {
                        $absentDates[$date->format('Y-m-d')] = $absentType;
                    }
                }

                $employeeReport = [];

                foreach ($dateRange as $date) {
                    $dateStr = $date->format('Y-m-d');
                    
                    // Check if there's attendance for this shift-aware date
                    $dayAttendances = $groupedByShiftDate->get($dateStr, collect());

                    // Cek apakah ada cuti atau izin pada tanggal tersebut
                    if (isset($leaveDates[$dateStr])) {
                        $timeRange = 'L';
                    } elseif (isset($absentDates[$dateStr])) {
                        $timeRange = $absentDates[$dateStr];
                    } elseif ($dayAttendances->isNotEmpty()) {
                        $sorted = $dayAttendances->sortBy('timestamp');
                        $checkIn = $sorted->first();
                        $checkOut = $sorted->last();

                        // Use ShiftHelper to format the time range (already includes overnight indicator)
                        $timeRange = ShiftHelper::formatAttendanceTimeRange($checkIn, $checkOut, $checkIn->shift);
                    } else {
                        $timeRange = '-';
                    }

                    $employeeReport[$dateStr] = $timeRange;
                }

                $this->reports->push([
                    'employee_id' => $employee->id,
                    'name' => $employee->user->name,
                    'attendance_data' => $employeeReport
                ]);
            }

        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage());
        }
    }

    #[On('reset-attendance-preview')]
    public function resetPreview()
    {
        $this->startDate = null;
        $this->endDate = null;
        $this->selectedEmployees = [];
        $this->countDays = 0;
        $this->reports = collect();
    }

    #[On('export-attendance-data')]
    public function exportAttendanceData($employees, $startDate, $endDate, $shifts = [])
    {
        try {
            // Generate the report data first
            $this->preview($employees, $startDate, $endDate, $shifts);
            
            if ($this->reports && $this->reports->isNotEmpty()) {
                $fileName = 'attendance_report_' . Carbon::parse($startDate)->format('Y-m-d') . '_to_' . Carbon::parse($endDate)->format('Y-m-d') . '.xlsx';
                
                return Excel::download(
                    new AttendanceReportExport($startDate, $endDate, $employees, $this->reports->toArray(), $shifts),
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
                $fileName = 'attendance_report_' . Carbon::parse($this->startDate)->format('Y-m-d') . '_to_' . Carbon::parse($this->endDate)->format('Y-m-d') . '.xlsx';
                
                return Excel::download(
                    new AttendanceReportExport($this->startDate, $this->endDate, $this->selectedEmployees, $this->reports->toArray(), $this->selectedShifts),
                    $fileName
                );
            } else {
                $this->alert('warning', 'No data available for export. Please generate the report first.');
            }
        } catch (\Exception $e) {
            $this->alert('error', 'Export failed: ' . $e->getMessage());
        }
    }

    private function getAbsentTypeCode($typeAbsent)
    {
        // Konversi type_absent ke kode yang sesuai
        switch (strtolower($typeAbsent)) {
            case 'sakit':
            case 'sick':
                return 'S';
            case 'izin':
            case 'permit':
                return 'I';
            default:
                return 'A'; // Default absent
        }
    }

    public function render()
    {
        return view('livewire.report.attendance-preview', [
            'dateRange' => $this->startDate && $this->endDate ? new DatePeriod(
                Carbon::parse($this->startDate),
                CarbonInterval::day(),
                Carbon::parse($this->endDate)
            ) : collect()
        ]);
    }
}
