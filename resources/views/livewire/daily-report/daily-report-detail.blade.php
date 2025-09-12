<div>
    @livewire('component.page.breadcrumb', ['breadcrumbs' => [['name' => __('ems.application'), 'url' => '/'], ['name' => __('ems.daily_report'), 'url' => route('daily-report.index')], ['name' => __('ems.daily_report_detail') . ' ' . $employee->user->name . ' ' . $date, 'url' => route('daily-report.detail', $daily_report->id)]]], key('breadcrumb'))

    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h4>{{ __('ems.date') }}</h4>
                    <p class="mb-0">{{ $date }}</p>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4>{{ __('ems.recipients') }}</h4>
                    @foreach ($recipients as $recipient)
                        <p class="mb-0">{{ $recipient->employee->user->name }}</p>
                    @endforeach
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4>{{ __('ems.read_by') }}</h4>
                    @if ($reads->count() > 0)
                        @foreach ($reads as $read)
                            <p class="mb-0">{{ $read->employee->user->name }}</p>
                        @endforeach
                    @else
                        <p class="mb-0">{{ __('ems.no_one') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-4">{{ __('ems.description') }}</h4>
                    {!! $daily_report->description !!}
                </div>
            </div>
        </div>
    </div>
</div>
