<div>
    @livewire('component.page.breadcrumb', ['breadcrumbs' => [['name' => __('ems.application'), 'url' => '/'], ['name' => __('ems.absent_request'), 'url' => route('absent-request.index')], ['name' => __('ems.view'), 'url' => '#']]], 'breadcrumb')

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="flex-grow-1">
                            <h4 class="card-title mb-0">{{ __('ems.absent_request_detail') }}</h4>
                        </div>
                        <div class="flex-shrink-0">
                            @if($absent_request->is_approved)
                                <span class="badge badge-soft-success font-size-12">{{ __('ems.approved') }}</span>
                            @elseif($absent_request->isRejectedByRecipients())
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
                                <p class="text-muted">{{ $absent_request->employee->user->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('ems.employee_id') }}:</label>
                                <p class="text-muted">{{ $absent_request->employee_id }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('ems.start_date') }}:</label>
                                <p class="text-muted">{{ $absent_request->start_date->format('d F Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('ems.end_date') }}:</label>
                                <p class="text-muted">{{ $absent_request->end_date->format('d F Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('ems.duration') }}:</label>
                                <p class="text-muted">{{ $absent_request->total_days }} {{ __('ems.days') }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('ems.type') }}:</label>
                                <p class="text-muted">
                                    <span class="badge badge-soft-info">{{ ucfirst($absent_request->type_absent ?? 'N/A') }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('ems.request_date') }}:</label>
                                <p class="text-muted">{{ $absent_request->created_at->format('d F Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('ems.notes') }}:</label>
                        <div class="border rounded p-3 bg-light">
                            <p class="mb-0">{{ $absent_request->notes ?? __('ems.no_notes_provided') }}</p>
                        </div>
                    </div>

                    @if($absent_request->file_path && $absent_request->file_url)
                        <div class="mb-3">
                            <label class="form-label fw-bold">{{ __('ems.attachment') }}:</label>
                            @livewire('component.file-preview', [
                                'fileUrl' => $absent_request->file_url,
                                'fileName' => basename($absent_request->file_path)
                            ],  'file-preview-' . $absent_request->id)
                        </div>
                    @endif
                </div>
            </div>

            <!-- Validation History -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('ems.validation_history') }}</h5>
                    @forelse($absent_request->validates as $validation)
                        <div class="border rounded p-3 mb-3 {{ $validation->status == 'approved' ? 'border-success bg-light-success' : 'border-danger bg-light-danger' }}">
                            <div class="d-flex align-items-center mb-2">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $validation->employee->user->name }}</h6>
                                    <small class="text-muted">{{ $validation->created_at->format('d F Y, H:i') }}</small>
                                </div>
                                <div class="flex-shrink-0">
                                    @if($validation->status == 'approved')
                                        <span class="badge badge-soft-success">{{ __('ems.approved') }}</span>
                                    @else
                                        <span class="badge badge-soft-danger">{{ __('ems.rejected') }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($validation->notes)
                                <p class="mb-0"><strong>{{ __('ems.notes') }}:</strong> {{ $validation->notes }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted">{{ __('ems.no_validation_history') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Recipients -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('ems.approvals') }}</h5>
                    @foreach($absent_request->recipients as $recipient)
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $recipient->employee->user->name }}</h6>
                            </div>
                            <div class="flex-shrink-0">
                                @if($absent_request->reads()->where('employee_id', $recipient->employee_id)->exists())
                                    <i class="mdi mdi-check-circle text-success" title="{{ __('ems.read') }}"></i>
                                @else
                                    <i class="mdi mdi-clock text-warning" title="{{ __('ems.unread') }}"></i>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Calendar -->
            <div class="card" wire:ignore>
                <div class="card-body">
                    <h5 class="card-title">{{ __('ems.calendar') }}</h5>
                    <div id="calendar"></div>
                </div>
            </div>

            <!-- Action Panel -->
            @if($recipientStatus && !$isApprovedRecipient && !$absent_request->is_approved)
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('ems.action_required') }}</h5>

                        <div class="d-grid gap-2 mt-2">
                            <button type="button" class="btn btn-success" wire:click="approveConfirm" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="approveConfirm">
                                    <i class="mdi mdi-check me-1"></i> {{ __('ems.approve') }}
                                </span>
                                <span wire:loading wire:target="approve">{{ __('ems.processing') }}</span>
                            </button>
                            <button type="button" class="btn btn-danger" wire:click="rejectConfirm" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="rejectConfirm">
                                    <i class="mdi mdi-close me-1"></i> {{ __('ems.reject') }}
                                </span>
                                <span wire:loading wire:target="reject">{{ __('ems.processing') }}</span>
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
                        backgroundColor: '#007bff', // Warna latar belakang rentang
                        borderColor: '#007bff'
                    }],
                    themeSystem: 'bootstrap',
                });
                calendar.render();
            });
        </script>
    @endpush
</div>
