<div>
    <div class="row">
        @if ($overtime_requests->count() > 0)
            @foreach ($overtime_requests as $overtime_request)
                @livewire('overtime-request.overtime-request-item', ['overtime_request' => $overtime_request], key($overtime_request->id))
            @endforeach
        @else
            <div class="col-md">
                <div class="card">
                    <div class="card-body text-center">
                        <h4 class="card-title">NO DATA</h4>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
