<div>
    @livewire('component.page.breadcrumb', ['breadcrumbs' => [['name' => __('ems.application'), 'url' => '/'], ['name' => __('ems.financial_request'), 'url' => route('financial-request.index')], ['name' => __('ems.detail'), 'url' => '#']]], key('breadcrumb'))

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="flex-grow-1">
                            <h4 class="card-title mb-0">{{ __('ems.financial_request_detail') }}</h4>
                        </div>
                        <div class="flex-shrink-0">
                            @if($financial_request->is_approved)
                                <span class="badge badge-soft-success font-size-12">{{ __('ems.approved') }}</span>
                            @elseif($financial_request->isRejectedByRecipients())
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
                                <p class="text-muted">{{ $financial_request->employee->user->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('ems.employee_id') }}:</label>
                                <p class="text-muted">{{ $financial_request->employee_id }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('ems.title') }}:</label>
                                <p class="text-muted">{{ $financial_request->title }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">{{ __('ems.type') }}:</label>
                                <p class="text-muted">
                                    <span class="badge badge-soft-info">{{ $financial_request->financialType->name ?? 'N/A' }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Amount:</label>
                                <p class="text-muted">
                                    <span class="h5 text-primary">Rp {{ number_format($financial_request->amount, 0, ',', '.') }}</span>
                                </p>
                            </div>
                        </div>
                       
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Request Date:</label>
                                <p class="text-muted">{{ $financial_request->created_at->format('d F Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Notes:</label>
                        <div class="border rounded p-3 bg-light">
                            <div>{!! $financial_request->notes ?? 'No notes provided.' !!}</div>
                        </div>
                    </div>

                    @if($financial_request->file_path)
                        <div class="mb-3">
                            <label class="form-label fw-bold">Attachment:</label>
                            <div class="border rounded p-3 bg-light">
                                <a href="{{ $financial_request->file_url }}" target="_blank" class="btn btn-soft-primary btn-sm">
                                    <i class="mdi mdi-file-download"></i> Download Attachment
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Validation History -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Validation History</h5>
                    @forelse($financial_request->validates as $validation)
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
                    @foreach($financial_request->recipients as $recipient)
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $recipient->employee->user->name }}</h6>
                            </div>
                            <div class="flex-shrink-0">
                                @if($financial_request->reads()->where('employee_id', $recipient->employee_id)->exists())
                                    <i class="mdi mdi-check-circle text-success" title="Read"></i>
                                @else
                                    <i class="mdi mdi-clock text-warning" title="Unread"></i>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Action Panel -->
            @if($recipientStatus && !$isApprovedRecipient && !$financial_request->is_approved)
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Action Required</h5>
                        
                        <div class="d-grid gap-2 mt-2">
                            <button type="button" class="btn btn-success" wire:click="approveConfirm" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="approveConfirm">
                                    <i class="mdi mdi-check me-1"></i> Approve
                                </span>
                                <span wire:loading wire:target="approve">Processing...</span>
                            </button>
                            <button type="button" class="btn btn-danger" wire:click="rejectConfirm" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="rejectConfirm">
                                    <i class="mdi mdi-close me-1"></i> Reject
                                </span>
                                <span wire:loading wire:target="reject">Processing...</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
