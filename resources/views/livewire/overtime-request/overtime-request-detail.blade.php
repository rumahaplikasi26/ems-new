<div>
    @livewire('component.page.breadcrumb', ['breadcrumbs' => [['name' => 'Application', 'url' => '/'], ['name' => 'Overtime Request', 'url' => route('overtime-request.index')], ['name' => 'Detail', 'url' => '#']]], key('breadcrumb'))

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="flex-grow-1">
                            <h4 class="card-title mb-0">Overtime Request Detail</h4>
                        </div>
                        <div class="flex-shrink-0">
                            @if($overtime_request->is_approved)
                                <span class="badge badge-soft-success font-size-12">Approved</span>
                            @elseif($overtime_request->isRejectedByRecipients())
                                <span class="badge badge-soft-danger font-size-12">Rejected</span>
                            @else
                                <span class="badge badge-soft-warning font-size-12">Pending</span>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Employee:</label>
                                <p class="text-muted">{{ $overtime_request->employee->user->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Employee ID:</label>
                                <p class="text-muted">{{ $overtime_request->employee_id }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Start Date & Time:</label>
                                <p class="text-muted">{{ $overtime_request->start_date->format('d F Y, H:i') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">End Date & Time:</label>
                                <p class="text-muted">{{ $overtime_request->end_date->format('d F Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Duration:</label>
                                <p class="text-muted">{{ number_format($overtime_request->duration, 1) }} hours ({{ $overtime_request->duration_in_days }} days)</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Priority:</label>
                                <p class="text-muted">
                                    @if($overtime_request->priority == 'major')
                                        <span class="badge badge-soft-danger">Major</span>
                                    @else
                                        <span class="badge badge-soft-success">Minor</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Request Date:</label>
                                <p class="text-muted">{{ $overtime_request->created_at->format('d F Y, H:i') }}</p>
                            </div>
                        </div>
                    </div>


                    <div class="mb-3">
                        <label class="form-label fw-bold">Reason:</label>
                        <div class="border rounded p-3 bg-light">
                            <p class="mb-0">{{ $overtime_request->reason }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validation History -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Validation History</h5>
                    @forelse($overtime_request->validates as $validation)
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
                    <h5 class="card-title">Recipients</h5>
                    @foreach($overtime_request->recipients as $recipient)
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $recipient->employee->user->name }}</h6>
                            </div>
                            <div class="flex-shrink-0">
                                @if($overtime_request->reads()->where('employee_id', $recipient->employee_id)->exists())
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
            @if(!$overtime_request->validates()->where('employee_id', auth()->user()->employee->id)->exists() && !$overtime_request->is_approved)
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Action Required</h5>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes (Optional for approval, Required for rejection)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" wire:model="notes" rows="3" 
                                      placeholder="Add your notes here..."></textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success" wire:click="approve" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="approve">
                                    <i class="mdi mdi-check me-1"></i> Approve
                                </span>
                                <span wire:loading wire:target="approve">Processing...</span>
                            </button>
                            <button type="button" class="btn btn-danger" wire:click="reject" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="reject">
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
