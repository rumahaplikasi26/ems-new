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
    ];

    protected $paginationTheme = 'bootstrap';

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

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function resetFilter()
    {
        $this->reset(['search', 'start_date', 'end_date']);
        $this->resetPage();
    }


    public function updatedSearch()
    {
        $this->resetPage();
        // Debug for server deployment
        if (config('app.debug')) {
            \Log::info('Search updated', ['search' => $this->search]);
        }
    }

    public function updatedStart_date()
    {
        $this->resetPage();
        // Debug for server deployment
        if (config('app.debug')) {
            \Log::info('Start date updated', ['start_date' => $this->start_date]);
        }
    }

    public function updatedEnd_date()
    {
        $this->resetPage();
        // Debug for server deployment
        if (config('app.debug')) {
            \Log::info('End date updated', ['end_date' => $this->end_date]);
        }
    }

    public function updatedPerPage()
    {
        $this->resetPage();
        // Debug for server deployment
        if (config('app.debug')) {
            \Log::info('Per page updated', ['perPage' => $this->perPage]);
        }
    }

    public function render()
    {
        // Debug filter values for server deployment
        if (config('app.debug')) {
            \Log::info('Filter values', [
                'search' => $this->search,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'perPage' => $this->perPage,
            ]);
        }

        // Build base query with filters - more robust for server deployment
        $baseQuery = Attendance::with(['employee.user', 'machine', 'site', 'attendanceMethod', 'shift'])
            ->when(!empty($this->search), function ($query) {
                $searchTerm = trim($this->search);
                $query->whereHas('employee.user', function ($query) use ($searchTerm) {
                    $query->where('name', 'like', '%' . $searchTerm . '%');
                });
            })
            ->when(!empty($this->start_date), function ($query) {
                $startDate = \Carbon\Carbon::parse($this->start_date)->format('Y-m-d');
                $query->whereDate('timestamp', '>=', $startDate);
            })
            ->when(!empty($this->end_date), function ($query) {
                $endDate = \Carbon\Carbon::parse($this->end_date)->format('Y-m-d');
                $query->whereDate('timestamp', '<=', $endDate);
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

        // Get all data first for proper grouping, then paginate
        $allAttendances = $attendances->orderBy('timestamp', 'desc')->get();

        // Group attendance by employee and shift-aware date
        $groupedAttendance = $allAttendances->groupBy('employee_id')->map(function ($employeeAttendances) {
            return $employeeAttendances->groupBy(function ($attendance) {
                return ShiftHelper::getAttendanceDate($attendance->timestamp, $attendance->shift);
            });
        });

        // Debug grouping for server deployment
        if (config('app.debug')) {
            \Log::info('Attendance Grouping Debug', [
                'total_attendance_records' => $allAttendances->count(),
                'total_employees' => $groupedAttendance->count(),
                'grouped_data' => $groupedAttendance->map(function ($dates, $employeeId) {
                    return [
                        'employee_id' => $employeeId,
                        'dates_count' => $dates->count(),
                        'dates' => $dates->keys()->toArray()
                    ];
                })->toArray()
            ]);
        }

        // Transform grouped data into display format
        $displayData = collect();
        foreach ($groupedAttendance as $employeeId => $datesData) {
            foreach ($datesData as $date => $dayAttendances) {
                $sorted = $dayAttendances->sortBy('timestamp');
                $checkIn = $sorted->first();
                $checkOut = $sorted->last();
                
                // Only show if there's actual attendance data
                if ($checkIn) {
                    // In this system, each attendance record is either check-in or check-out
                    // We need to find the check-out for the same employee on the same day
                    $employeeId = $checkIn->employee_id;
                    
                    // Ensure timestamp is a Carbon instance
                    $checkInTimestamp = is_string($checkIn->timestamp) ? 
                        \Carbon\Carbon::parse($checkIn->timestamp) : 
                        $checkIn->timestamp;
                    
                    $date = $checkInTimestamp->format('Y-m-d');
                    
                    // Find check-out attendance for the same employee on the same day
                    $checkOut = Attendance::where('employee_id', $employeeId)
                        ->whereDate('timestamp', $date)
                        ->where('id', '!=', $checkIn->id)
                        ->where('timestamp', '>', $checkIn->timestamp)
                        ->orderBy('timestamp', 'desc')
                        ->first();
                    
                    // Use the already parsed checkInTimestamp
                    $checkOutTimestamp = $checkOut ? 
                        (is_string($checkOut->timestamp) ? 
                            \Carbon\Carbon::parse($checkOut->timestamp) : 
                            $checkOut->timestamp) : null;

                    // Debug checkout data for server deployment
                    if (config('app.debug')) {
                        \Log::info('Checkout Debug', [
                            'attendance_id' => $checkIn->id,
                            'employee_id' => $employeeId,
                            'date' => $date,
                            'check_out_exists' => $checkOut ? true : false,
                            'check_out_timestamp' => $checkOutTimestamp ? $checkOutTimestamp->format('Y-m-d H:i:s') : null,
                        ]);
                    }

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
                            'approved_at_formatted' => $checkIn->approved_at ? 
                                (is_string($checkIn->approved_at) ? 
                                    \Carbon\Carbon::parse($checkIn->approved_at)->format('d-m-Y H:i:s') :
                                    $checkIn->approved_at->format('d-m-Y H:i:s')) : null,
                        ],
                        'check_out' => $checkOut ? [
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
                            'approved_at_formatted' => $checkOut->approved_at ? 
                                (is_string($checkOut->approved_at) ? 
                                    \Carbon\Carbon::parse($checkOut->approved_at)->format('d-m-Y H:i:s') :
                                    $checkOut->approved_at->format('d-m-Y H:i:s')) : null,
                        ] : null,
                        'employee_id' => $employeeId,
                        'date' => $date,
                        'shift_date' => ShiftHelper::getAttendanceDate($checkInTimestamp, $checkIn->shift),
                        'duration_string' => $formattedDuration ?? '-',
                        'duration' => $duration ?? 0,
                        'status' => ShiftHelper::getAttendanceStatus($checkIn, $checkOut, $checkIn->shift),
                        'time_range' => ShiftHelper::formatAttendanceTimeRange($checkIn, $checkOut, $checkIn->shift),
                    ]);
                }
            }
        }

        // Sort by date descending
        $sortedData = $displayData->sortByDesc('date')->values();
        
        // Manual pagination on processed data - robust for server deployment
        $currentPage = max(1, (int) request()->get('page', 1));
        $perPage = max(1, (int) $this->perPage);
        $total = $sortedData->count();
        $offset = ($currentPage - 1) * $perPage;
        $paginatedData = $sortedData->slice($offset, $perPage)->values();
        
        // Get base URL for pagination links (more robust for server deployment)
        $baseUrl = url()->current();
        
        // Create paginator with robust configuration
        $attendances = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedData,
            $total,
            $perPage,
            $currentPage,
            [
                'path' => $baseUrl,
                'pageName' => 'page',
            ]
        );
        
        // Preserve query parameters for filter persistence
        $queryParams = request()->except(['page']);
        if (!empty($queryParams)) {
            $attendances->appends($queryParams);
        }

        // Debug pagination for server deployment
        if (config('app.debug')) {
            \Log::info('Attendance Index Debug', [
                'environment' => app()->environment(),
                'server_info' => [
                    'php_version' => PHP_VERSION,
                    'laravel_version' => app()->version(),
                    // 'livewire_version' => \Livewire\Livewire::VERSION ?? 'unknown',
                ],
                'filters' => [
                    'search' => $this->search,
                    'start_date' => $this->start_date,
                    'end_date' => $this->end_date,
                    'perPage' => $this->perPage,
                ],
                'query_results' => [
                    'total_attendance_records' => $allAttendances->count(),
                    'total_employees' => $groupedAttendance->count(),
                    'processed_items' => $sortedData->count(),
                ],
                'pagination' => [
                    'current_page' => $attendances->currentPage(),
                    'per_page' => $attendances->perPage(),
                    'total' => $attendances->total(),
                    'total_pages' => $attendances->lastPage(),
                    'has_pages' => $attendances->hasPages(),
                    'has_more_pages' => $attendances->hasMorePages(),
                    'next_page_url' => $attendances->nextPageUrl(),
                    'previous_page_url' => $attendances->previousPageUrl(),
                ],
                'url_info' => [
                    'current_url' => request()->url(),
                    'full_url' => request()->fullUrl(),
                    'query_params' => request()->query(),
                ]
            ]);
        }

        return view('livewire.attendance.attendance-index', [
            'attendances' => $attendances
        ])->layout('layouts.app', ['title' => 'Attendance List']);
    }
}
