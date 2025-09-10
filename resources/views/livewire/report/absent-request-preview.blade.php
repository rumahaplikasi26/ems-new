<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="card-title mb-0">Absent Request Reports</h4>
            @if ($reports && $reports->isNotEmpty())
                <button class="btn btn-success btn-sm" wire:click="exportExcel" wire:loading.attr="disabled">
                    <i class="fas fa-file-excel"></i> Export Excel
                </button>
            @endif
        </div>
        @if ($reports)
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-sm">
                    <thead class="table-light text-center fw-bold text-uppercase align-middle">
                        <tr>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Date Range</th>
                            <th>Type</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $absentRequest)
                            <tr class="align-middle">
                                <td class="text-center"><small>{{ $absentRequest->employee_id }}</small></td>
                                <td><small>{{ $absentRequest->employee->user->name }}</small></td>
                                <td class="text-center"><small>{{ $absentRequest->start_date->format('d/m/Y') }} -
                                        {{ $absentRequest->end_date->format('d/m/Y') }}</small></td>
                                <td class="text-center"><small>{{ $absentRequest->type_absent ?? 'N/A' }}</small></td>
                                <td><small>{{ $absentRequest->notes ?? '-' }}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
