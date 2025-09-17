<div>
    @livewire('component.page.breadcrumb', ['breadcrumbs' => [['name' => __('ems.application'), 'url' => '/'], ['name' => __('ems.leave_request'), 'url' => route('leave-request.index')], ['name' => __('ems.detail'), 'url' => '#']]], key('breadcrumb'))

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="flex-grow-1">
                            <h4 class="card-title mb-0">{{ __('ems.leave_request_detail') }}</h4>
                        </div>
                        <div class="flex-shrink-0">
                            @if($leave_request->is_approved)
                                <span class="badge badge-soft-success font-size-12">{{ __('ems.approved') }}</span>
                            @elseif($leave_request->isRejectedByRecipients())
                                <span class="badge badge-soft-danger font-size-12">{{ __('ems.rejected') }}</span>
                            @else
                                <span class="badge badge-soft-warning font-size-12">{{ __('ems.pending') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('ems.employee') }}:</label>
                                <p class="text-muted">{{ $leave_request->employee->user->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('ems.employee_id') }}:</label>
                                <p class="text-muted">{{ $leave_request->employee_id }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('ems.start_date') }}:</label>
                                <p class="text-muted">{{ $leave_request->start_date->format('d F Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('ems.end_date') }}:</label>
                                <p class="text-muted">{{ $leave_request->end_date->format('d F Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Duration:</label>
                                <p class="text-muted">{{ $leave_request->total_days }} days</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Leave Remaining:</label>
                                <p class="text-muted">{{ $leave_request->employee->leave_remaining ?? 0 }} days</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Request Date:</label>
                                <p class="text-muted">{{ $leave_request->created_at->format('d F Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Notes:</label>
                        <div class="border rounded p-3 bg-light">
                            <p class="mb-0">{{ $leave_request->notes ?? 'No notes provided.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validation History -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Validation History</h5>
                    @forelse($leave_request->validates as $validation)
                        <div class="border rounded p-3 mb-3 {{ $validation->status == 'approved' ? 'border-success bg-light-success' : 'border-danger bg-light-danger' }}">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $validation->employee->user->name }}</h6>
                                    <small class="text-muted">{{ $validation->created_at->format('d F Y, H:i') }}</small>
                                </div>
                                <div class="flex-shrink-0">
                                    @if($validation->status == 'approved')
                                        <span class="badge badge-soft-success">Approved</span>
                                    @else
                                        <span class="badge badge-soft-danger">Rejected</span>
                                    @endif
                                </div>
                            </div>
                            @if($validation->notes)
                                <p class="mb-0"><strong>Notes:</strong> {{ $validation->notes }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted">No validation history yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Recipients -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('ems.approvals') }}</h5>
                    @foreach($leave_request->recipients as $recipient)
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $recipient->employee->user->name }}</h6>
                            </div>
                            <div class="flex-shrink-0">
                                @if($leave_request->reads()->where('employee_id', $recipient->employee_id)->exists())
                                    <i class="mdi mdi-check-circle text-success" title="Read"></i>
                                @else
                                    <i class="mdi mdi-clock text-warning" title="Unread"></i>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Calendar -->
            <div class="card" wire:ignore>
                <div class="card-body">
                    <h5 class="card-title">Calendar</h5>
                    <div id="calendar"></div>
                </div>
            </div>

            <!-- Action Panel -->
            @if($recipientStatus && !$isApprovedRecipient && !$leave_request->is_approved)
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Action Required</h5>
                       
                        <div class="d-grid gap-2 mt-2">
                            <button type="button" class="btn btn-success" wire:click="approveConfirm" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="approveConfirm">
                                    <i class="mdi mdi-check me-1"></i> Approve
                                </span>
                                <span wire:loading wire:target="approveConfirm">Processing...</span>
                            </button>
                            <button type="button" class="btn btn-danger" wire:click="rejectConfirm" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="rejectConfirm">
                                    <i class="mdi mdi-close me-1"></i> Reject
                                </span>
                                <span wire:loading wire:target="rejectConfirm">Processing...</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>

    @push('styles')
        <link href="{{ asset('libs/%40fullcalendar/core/main.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('libs/%40fullcalendar/daygrid/main.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('libs/%40fullcalendar/bootstrap/main.min.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('libs/%40fullcalendar/timegrid/main.min.css') }}" rel="stylesheet" type="text/css" />
    @endpush

    @push('js')
        <script src="{{ asset('libs/jquery-ui-dist/jquery-ui.min.js') }}"></script>
        <script src="{{ asset('libs/%40fullcalendar/core/main.min.js') }}"></script>
        <script src="{{ asset('libs/%40fullcalendar/bootstrap/main.min.js') }}"></script>
        <script src="{{ asset('libs/%40fullcalendar/daygrid/main.min.js') }}"></script>
        <script src="{{ asset('libs/%40fullcalendar/timegrid/main.min.js') }}"></script>
        <script src="{{ asset('libs/%40fullcalendar/interaction/main.min.js') }}"></script>

        <script>
            document.addEventListener('livewire:init', function() {
                var startDate = @json($start_date); // Format: 'YYYY-MM-DD'
                var endDate = @json($end_date); // Format: 'YYYY-MM-DD'

                // Fix: Add 1 day to end date for FullCalendar (exclusive end date)
                var endDatePlusOne = new Date(endDate);
                endDatePlusOne.setDate(endDatePlusOne.getDate() + 1);
                var endDateFormatted = endDatePlusOne.toISOString().split('T')[0];

                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    defaultView: 'dayGridMonth',
                    plugins: ["bootstrap", "interaction", "dayGrid", "timeGrid"],
                    header: false,
                    editable: false, // Nonaktifkan interaksi
                    selectable: false, // Nonaktifkan pemilihan tanggal
                    selectMirror: false, // Nonaktifkan pemilihan tanggal
                    displayEventTime: false, // Nonaktifkan menampilkan waktu
                    displayEventEnd: false, // Nonaktifkan menampilkan Waktu
                    events: [{
                        start: startDate,
                        end: endDateFormatted, // Use the fixed end date
                        display: 'background', // Tampilkan rentang sebagai latar belakang
                        backgroundColor: '#28a745', // Green color for leave
                        borderColor: '#28a745'
                    }],
                    themeSystem: 'bootstrap',
                });
                calendar.render();
            });
        </script>
    @endpush
</div>
