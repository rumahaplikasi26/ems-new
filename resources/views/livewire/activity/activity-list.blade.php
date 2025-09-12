<div>
    <div class="table-responsive">
        <table class="table project-list-table table-nowrap align-middle table-borderless">
            <thead>
                <tr>
                    <th scope="col" style="width: 100px">#</th>
                    <th scope="col">{{ __('ems.user') }}</th>
                    <th scope="col">{{ __('ems.timestamp') }}</th>
                    <th scope="col">{{ __('ems.description') }}</th>
                </tr>
            </thead>
            <tbody>
                @if ($activities)
                    @foreach ($activities as $activity)
                        @livewire('activity.activity-item', ['activity' => $activity, 'iteration' => $loop->iteration], key($activity->id))
                    @endforeach
                @else
                    <tr>
                        <td colspan="5" class="text-center fw-bold">{{ __('ems.no_data') }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
