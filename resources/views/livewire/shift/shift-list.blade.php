<div>
    <div class="row">
        @foreach ($shifts as $shift)
            @livewire('shift.shift-item', ['shift' => $shift], key($shift->id))
        @endforeach
    </div>
</div>