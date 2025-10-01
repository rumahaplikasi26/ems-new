<tr>
    <td>
        <div class="avatar-sm">
            <span class="avatar-title bg-success text-white rounded">
                {{ $day }}
            </span>
        </div>
    </td>
    <td>
        @if ($image_url)
            <a href="javascript: void(0);" class="d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ $employee_name }}">
                <img src="{{ $image_url }}" alt="{{ $employee_name }}" class="rounded-circle avatar-sm">
            </a>
        @else
            <div class="avatar-sm">
                <span class="avatar-title bg-success text-white rounded">
                    {{ strtoupper(substr($employee_name, 0, 1)) }}
                </span>
            </div>
        @endif
        <h5 class="text-truncate font-size-14">
            <a href="javascript: void(0);" class="text-dark">{{ $employee_name }}</a>
        </h5>
        <p class="text-muted mb-0">{{ $employee_email ?? 'No email' }}</p>
    </td>
    <td>
        <div>
            <span class="badge bg-info">{{ $date }}</span>
            @if (isset($shift_date) && $shift_date !== $date)
                <br><small class="text-muted">Shift Date:
                    {{ $shift_date }}</small>
            @endif
        </div>
    </td>
    <td>
        <div>
            <span class="badge bg-info">{{ $shift_name }}</span>
        </div>
    </td>
    <td>
        <div>
            <span
                class="text-success fw-bold">{{ $time_formatted ?? date('H:i:s', strtotime($attendance->timestamp)) }}</span>
            @if ($attendance && $attendance->site)
                <br><small class="text-muted">{{ $attendance->site->name }}</small>
            @endif
            @if ($attendance && $attendance->attendanceMethod)
                <br><small class="text-muted">{{ $attendance->attendanceMethod->name }}</small>
            @endif
        </div>
    </td>
    <td>
        <small class="text-muted">{{ $timestamp ? date('d-m-Y H:i:s', strtotime($timestamp)) : 'N/A' }}</small>
    </td>
    <td>
        @if ($approvedBy && $approvedBy !== '-')
            <small class="text-success fw-bold">{{ $approvedBy }}</small>
        @else
            <span class="text-muted">-</span>
        @endif
    </td>
    <td>
        @if ($approvedAt && $approvedAt !== '-')
            <small class="text-muted">{{ $approvedAt }}</small>
        @else
            <span class="text-muted">-</span>
        @endif
    </td>
    <td>
        <div>
            @if ($distanceFormatted)
                {!! $distanceFormatted !!}
            @endif
            @if ($noteExcerpt)
                <br>{!! $noteExcerpt !!}
            @endif
        </div>
    </td>
</tr>