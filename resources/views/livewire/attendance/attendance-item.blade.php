<tr>
    <td>
        @if ($image_url)
            <a href="javascript: void(0);" class="d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ $employee_name }}">
                <img src="{{ $image_url }}" alt="{{ $employee_name }}" class="rounded-circle avatar-sm">
            </a>
        @else
            <div class="avatar-sm">
                <span class="avatar-title rounded-circle bg-success text-white font-size-16">
                    {{ strtoupper(substr($employee_name, 0, 1)) }}
                </span>
            </div>
        @endif
    </td>
    <td>
        <h5 class="text-truncate font-size-14">
            <a href="javascript: void(0);" class="text-dark">{{ $employee_name }}</a>
        </h5>
        <p class="text-muted mb-0">{{ $email }}</p>
    </td>
    <td>
        <strong>{{ $timestamp }}</strong>
    </td>
    <td>
        @if($shift_name)
            <span class="badge bg-primary">{{ $shift_name }}</span>
        @else
            <span class="text-muted">-</span>
        @endif
    </td>
    <td>
        {!! $distanceFormatted !!}
    </td>
    <td>
        <span class="badge bg-primary">{{ $site_name }}</span>
    </td>
    <td>
        <a href="https://www.google.com/maps/search/{{ $latitude }},{{ $longitude }}" target="_blank">{{ $longitude }}, {{ $latitude }}</a>
    </td>
    <td>
        <span class="text-wrap">
            {!! $noteExcerpt !!}
        </span>
    </td>
    <td>
        <span class="text-muted">-</span>
    </td>
</tr>
