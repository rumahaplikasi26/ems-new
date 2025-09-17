<div>
    @livewire('component.page.breadcrumb', ['breadcrumbs' => [['name' => __('ems.application'), 'url' => '/'], ['name' => __('ems.overtime_request'), 'url' => route('overtime-request.index')], ['name' => __('ems.detail'), 'url' => '#']]], key('breadcrumb'))

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="flex-grow-1">
                            <h4 class="card-title mb-0">{{ __('ems.overtime_request_detail') }}</h4>
                        </div>
                        <div class="flex-shrink-0">
                            @if($overtime_request->is_approved)
                                <span class="badge badge-soft-success font-size-12">{{ __('ems.approved') }}</span>
                            @elseif($overtime_request->isRejectedByRecipients())
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
                                <p class="text-muted">{{ $overtime_request->employee->user->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('ems.employee_id') }}:</label>
                                <p class="text-muted">{{ $overtime_request->employee_id }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('ems.start_date_time') }}:</label>
                                <p class="text-muted">{{ $overtime_request->start_date->format('d F Y, H:i') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('ems.end_date_time') }}:</label>
                                <p class="text-muted">{{ $overtime_request->end_date->format('d F Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('ems.duration') }}:</label>
                                <p class="text-muted">{{ number_format($overtime_request->duration, 1) }} {{ __('ems.hours') }} ({{ $overtime_request->duration_in_days }} {{ __('ems.days') }})</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('ems.priority') }}:</label>
                                <p class="text-muted">
                                    @if($overtime_request->priority == 'major')
                                        <span class="badge badge-soft-danger">{{ __('ems.major') }}</span>
                                    @else
                                        <span class="badge badge-soft-success">{{ __('ems.minor') }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('ems.request_date') }}:</label>
                                <p class="text-muted">{{ $overtime_request->created_at->format('d F Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>


                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('ems.reason') }}:</label>
                        <div class="border rounded p-3 bg-light">
                            <p class="mb-0">{{ $overtime_request->reason }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validation History -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('ems.validation_history') }}</h5>
                    @forelse($overtime_request->validates as $validation)
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
                        <p class="text-muted">{{ __('ems.no_validation_history_yet') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Recipients -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ __('ems.approvals') }}</h5>
                    @foreach($overtime_request->recipients as $recipient)
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $recipient->employee->user->name }}</h6>
                            </div>
                            <div class="flex-shrink-0">
                                @if($overtime_request->reads()->where('employee_id', $recipient->employee_id)->exists())
                                    <i class="mdi mdi-check-circle text-success" title="{{ __('ems.read') }}"></i>
                                @else
                                    <i class="mdi mdi-clock text-warning" title="{{ __('ems.unread') }}"></i>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Action Panel -->
            @if($recipientStatus && !$isApprovedRecipient && !$overtime_request->is_approved)
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">{{ __('ems.action_required') }}</h5>
                        
                        <div class="d-grid gap-2 mt-2">
                            <button type="button" class="btn btn-success" wire:click="approveConfirm" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="approveConfirm">
                                    <i class="mdi mdi-check me-1"></i> {{ __('ems.approve') }}
                                </span>
                                <span wire:loading wire:target="approveConfirm">{{ __('ems.processing') }}</span>
                            </button>
                            <button type="button" class="btn btn-danger" wire:click="rejectConfirm" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="rejectConfirm">
                                    <i class="mdi mdi-close me-1"></i> {{ __('ems.reject') }}
                                </span>
                                <span wire:loading wire:target="rejectConfirm">{{ __('ems.processing') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
