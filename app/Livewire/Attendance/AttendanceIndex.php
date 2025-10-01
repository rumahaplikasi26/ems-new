<?php

namespace App\Livewire\Attendance;

use App\Livewire\BaseComponent;
use App\Models\Attendance;
use App\Helpers\ShiftHelper;
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
        'employee_id' => ['except' => ''],
    ];

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
        $this->resetPage();
    }

    public function render()
    {
        // Build base query with filters
        $baseQuery = Attendance::with(['employee.user', 'machine', 'site', 'attendanceMethod', 'shift'])
            ->when($this->search, function ($query) {
                $query->whereHas('employee.user', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->start_date, function ($query) {
                $query->whereDate('timestamp', '>=', $this->start_date);
            })
            ->when($this->end_date, function ($query) {
                $query->whereDate('timestamp', '<=', $this->end_date);
            });

        // Apply user permissions to the query
        if ($this->authUser->can('view:attendance-all')) {
            $attendances = $baseQuery;
        } else {
            // Check if user is a supervisor
            if ($this->authUser->employee && $this->authUser->employee->isSupervisor()) {
                // Get employee IDs under supervision
                $supervisedEmployeeIds = $this->authUser->employee->getSupervisedEmployeeIds();
                // Include supervisor's own attendance
                $supervisedEmployeeIds->push($this->authUser->employee->id);
                $attendances = $baseQuery->whereIn('employee_id', $supervisedEmployeeIds);
            } else {
                // Regular employee - only see their own attendance
                $attendances = $baseQuery->where('employee_id', $this->authUser->employee->id);
            }
        }

        // Get all data for processing
        $allAttendances = $attendances->orderBy('timestamp', 'desc')->get();

        // Group attendance by employee and shift-aware date
        $groupedAttendance = $allAttendances->groupBy('employee_id')->map(function ($employeeAttendances) {
            return $employeeAttendances->groupBy(function ($attendance) {
                return ShiftHelper::getAttendanceDate($attendance->timestamp, $attendance->shift);
            });
        });

        // Transform grouped data into display format
        $displayData = collect();
        foreach ($groupedAttendance as $employeeId => $datesData) {
            foreach ($datesData as $date => $dayAttendances) {
                $sorted = $dayAttendances->sortBy('timestamp');
                $checkIn = $sorted->first();
                $checkOut = $sorted->last();
                
                // Only show if there's actual attendance data
                if ($checkIn) {
                    $checkInTimestamp = new \DateTime($checkIn->timestamp);
                    $checkOutTimestamp = $checkOut && $checkIn->id !== $checkOut->id ? new \DateTime($checkOut->timestamp) : null;

                    $duration = null;
                    $formattedDuration = null;

                    if ($checkInTimestamp && $checkOutTimestamp) {
                        $interval = $checkInTimestamp->diff($checkOutTimestamp);
                        $hours = $interval->h + ($interval->days * 24); // Total hours
                        $minutes = $interval->i; // Minutes
                        $seconds = $interval->s; // Seconds

                        $duration = $hours + ($minutes / 60) + ($seconds / 3600); // Total hours in decimal

                        // Format duration as "H hours, M minutes, S seconds"
                        $formattedDuration = sprintf('%d hours, %d minutes, %d seconds', $hours, $minutes, $seconds);
                    }

                    $displayData->push([
                        'id' => $checkIn->uid . '-' . ($checkOut ? $checkOut->uid : 'null'),
                        'employee' => [
                            'id' => $checkIn->employee->id,
                            'name' => $checkIn->employee->user->name,
                            'email' => $checkIn->employee->user->email,
                            'avatar_url' => $checkIn->employee->user->avatar_url,
                        ],
                        'check_in' => [
                            'id' => $checkIn->id,
                            'timestamp' => $checkIn->timestamp,
                            'attendance_method' => $checkIn->attendanceMethod ? [
                                'id' => $checkIn->attendanceMethod->id,
                                'name' => $checkIn->attendanceMethod->name,
                            ] : null,
                            'machine' => $checkIn->machine ? [
                                'id' => $checkIn->machine->id,
                                'name' => $checkIn->machine->name,
                            ] : null,
                            'site' => $checkIn->site ? [
                                'id' => $checkIn->site->id,
                                'name' => $checkIn->site->name,
                                'longitude' => $checkIn->site->longitude,
                                'latitude' => $checkIn->site->latitude,
                            ] : null,
                            'shift' => $checkIn->shift ? [
                                'id' => $checkIn->shift->id,
                                'name' => $checkIn->shift->name,
                                'start_time' => $checkIn->shift->start_time,
                                'end_time' => $checkIn->shift->end_time,
                                'display_name' => ShiftHelper::getShiftDisplayName($checkIn->shift),
                                'is_overnight' => ShiftHelper::isOvernightShift($checkIn->shift),
                            ] : null,
                            'uid' => $checkIn->uid,
                            'longitude' => $checkIn->longitude,
                            'latitude' => $checkIn->latitude,
                            'image_url' => $checkIn->image_url ?? null,
                            'distance' => $checkIn->distance,
                            'notes' => $checkIn->notes,
                            'approved_by' => $checkIn->approved_by,
                            'approved_at' => $checkIn->approved_at,
                            'approved_by_name' => $checkIn->approvedBy?->name,
                            'approved_at_formatted' => $checkIn->approved_at?->format('d-m-Y H:i:s'),
                        ],
                        'check_out' => $checkOut && $checkIn->id !== $checkOut->id ? [
                            'id' => $checkOut->id,
                            'timestamp' => $checkOut->timestamp,
                            'attendance_method' => $checkOut->attendanceMethod ? [
                                'id' => $checkOut->attendanceMethod->id,
                                'name' => $checkOut->attendanceMethod->name,
                            ] : null,
                            'machine' => $checkOut->machine ? [
                                'id' => $checkOut->machine->id,
                                'name' => $checkOut->machine->name,
                            ] : null,
                            'site' => $checkOut->site ? [
                                'id' => $checkOut->site->id,
                                'name' => $checkOut->site->name,
                                'longitude' => $checkOut->site->longitude,
                                'latitude' => $checkOut->site->latitude,
                            ] : null,
                            'shift' => $checkOut->shift ? [
                                'id' => $checkOut->shift->id,
                                'name' => $checkOut->shift->name,
                                'start_time' => $checkOut->shift->start_time,
                                'end_time' => $checkOut->shift->end_time,
                                'display_name' => ShiftHelper::getShiftDisplayName($checkOut->shift),
                                'is_overnight' => ShiftHelper::isOvernightShift($checkOut->shift),
                            ] : null,
                            'uid' => $checkOut->uid,
                            'longitude' => $checkOut->longitude,
                            'latitude' => $checkOut->latitude,
                            'image_url' => $checkOut->image_url ?? null,
                            'distance' => $checkOut->distance,
                            'notes' => $checkOut->notes,
                            'approved_by' => $checkOut->approved_by,
                            'approved_at' => $checkOut->approved_at,
                            'approved_by_name' => $checkOut->approvedBy?->name,
                            'approved_at_formatted' => $checkOut->approved_at?->format('d-m-Y H:i:s'),
                        ] : null,
                        'employee_id' => $employeeId,
                        'date' => $date,
                        'shift_date' => ShiftHelper::getAttendanceDate($checkIn->timestamp, $checkIn->shift),
                        'duration_string' => $formattedDuration ?? '-',
                        'duration' => $duration ?? 0,
                        'status' => ShiftHelper::getAttendanceStatus($checkIn, $checkOut, $checkIn->shift),
                        'time_range' => ShiftHelper::formatAttendanceTimeRange($checkIn, $checkOut, $checkIn->shift),
                    ]);
                }
            }
        }

        // Sort by date descending and apply pagination
        $sortedData = $displayData->sortByDesc('date')->values();
        
        // Apply pagination using Livewire's built-in pagination
        $currentPage = $this->getPage();
        $perPage = $this->perPage;
        $total = $sortedData->count();
        $offset = ($currentPage - 1) * $perPage;
        $paginatedData = $sortedData->slice($offset, $perPage)->values();
        
        // Create paginator manually for Livewire compatibility
        $attendances = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedData,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'pageName' => 'page',
            ]
        );
        
        // Set pagination info for Livewire
        $this->setPage($currentPage);

        // dd($attendances);

        return view('livewire.attendance.attendance-index', [
            'attendances' => $attendances
        ])->layout('layouts.app', ['title' => 'Attendance List']);
    }
}
