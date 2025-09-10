<div class="col-lg-4 col-xl-3">
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-start mb-3">
                <div class="flex-grow-1">
                    @if($isApproved)
                        <span class="badge badge-soft-success">Approved</span>
                    @elseif($isRejected)
                        <span class="badge badge-soft-danger">Rejected</span>
                    @else
                        <span class="badge badge-soft-secondary">Pending</span>
                    @endif
                </div>
                <div class="flex-shrink-0">
                    <span class="badge badge-soft-danger">{{ $overtime_request->created_at->diffForHumans() }}</span>
                </div>
            </div>
            <div class="text-center mb-3">
                <div class="avatar-sm mx-auto align-self-center d-xl-block d-none">
                    <span class="avatar-title rounded-circle bg-primary bg-soft text-primary font-size-16">
                        {{ substr($user->name, 0, 1) }}
                    </span>
                </div>

                <h6 class="font-size-15 mt-3 mb-1">{{ $user->name }}</h6>
                <p class="mb-0 text-muted">{{ Str::limit($overtime_request->reason, 50) }}</p>
            </div>
            <div class="d-flex mb-3 justify-content-center gap-2 text-muted">
                <div class="text-center">
                    <small class="d-block">{{ $overtime_request->start_date->format('d M Y, H:i') }}</small>
                    <small class="text-muted">to</small>
                    <small class="d-block">{{ $overtime_request->end_date->format('d M Y, H:i') }}</small>
                    <span class="badge badge-soft-info mt-1">
                        {{ number_format($overtime_request->duration, 1) }}h ({{ $totalDays }} days)
                    </span>
                </div>
            </div>
            <div class="d-flex mb-3 justify-content-center">
                @if($overtime_request->priority == 'major')
                    <span class="badge badge-soft-danger">Major Priority</span>
                @else
                    <span class="badge badge-soft-success">Minor Priority</span>
                @endif
            </div>
            <div class="hstack gap-2 justify-content-center flex-wrap">
                @foreach ($recipientsWithStatus as $item)
                    <span class="badge {{ $item['badgeClass'] }} font-size-12" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="{{ ucfirst($item['status']) }}">
                        {{ $item['recipient']->employee->user->name }}
                    </span>
                @endforeach
            </div>

            <div class="d-flex gap-2 justify-content-center flex-wrap my-3">
                @if ($recipientStatus && !$isApprovedRecipient)
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Action <i class="mdi mdi-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu">
                            <!-- item-->
                            <a class="dropdown-item btn btn-soft-danger btn-sm" wire:click="rejectConfirm({{ $overtime_request->id }})" href="javascript:void(0);"><i class="mdi mdi-close"></i> Reject</a>
                            <a class="dropdown-item btn btn-soft-success btn-sm" wire:click="approveConfirm({{ $overtime_request->id }})" href="javascript:void(0);"><i class="mdi mdi-check"></i> Approve</a>
                        </div>
                    </div>
                @endif

                <a href="{{ route('overtime-request.detail', ['id' => $overtime_request->id]) }}"
                    class="btn btn-soft-warning btn-sm"><i class="mdi mdi-eye-outline"></i> View</a>

                @if (!$disableUpdate && !$disableUpdateApprove)
                    <a href="{{ route('overtime-request.edit', ['id' => $overtime_request->id]) }}"
                        class="btn btn-soft-primary btn-sm"><i class="mdi mdi-pencil-outline"></i> Edit</a>
                    <a href="javascript:void(0)" class="btn btn-soft-danger btn-sm"
                        wire:click="deleteConfirm({{ $overtime_request->id }})"><i class="mdi mdi-delete-outline"></i>
                        Delete</a>
                @endif
            </div>
        </div>
    </div>
</div>
