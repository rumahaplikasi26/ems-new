<div>
    <div class="row">
        @if ($announcements->count() > 0)
            @foreach ($announcements as $announcement)
                @livewire('announcement.announcement-item', ['announcement' => $announcement], key($announcement->id))
            @endforeach
        @else
            <div class="col-md">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="card-title">{{ __('ems.no_data') }}</h4>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
