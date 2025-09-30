<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="card-title mb-0">Overtime Request Reports</h4>
        </div>
        @if ($reports)
            <div class="table-responsive mt-4">
                <table class="table table-bordered table-sm">
                    <thead class="table-light text-center fw-bold text-uppercase align-middle">
                        <tr>
                            <th>Employee ID</th>
                            <th>Name</th>
                            <th>Start Date & Time</th>
                            <th>End Date & Time</th>
                            <th>Duration</th>
                            <th>Priority</th>
                            <th>Reason</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($reports as $overtimeRequest)
                            <tr class="align-middle">
                                <td class="text-center"><small>{{ $overtimeRequest->employee_id }}</small></td>
                                <td><small>{{ $overtimeRequest->employee->user->name }}</small></td>
                                <td class="text-center"><small>{{ $overtimeRequest->start_date->format('d/m/Y H:i') }}</small></td>
                                <td class="text-center"><small>{{ $overtimeRequest->end_date->format('d/m/Y H:i') }}</small></td>
                                <td class="text-center">
                                    <small>
                                        {{ number_format($overtimeRequest->duration, 1) }}h
                                        <br>
                                        ({{ $overtimeRequest->duration_in_days }} days)
                                    </small>
                                </td>
                                <td class="text-center">
                                    @if($overtimeRequest->priority == 'major')
                                        <span class="badge badge-soft-danger">Major</span>
                                    @else
                                        <span class="badge badge-soft-success">Minor</span>
                                    @endif
                                </td>
                                <td><small>{{ Str::limit($overtimeRequest->reason ?? '-', 50) }}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
