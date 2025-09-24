<?php

namespace App\Livewire\Shift;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class ShiftList extends Component
{
    #[Reactive]
    public $shifts;

    public function render()
    {
        return view('livewire.shift.shift-list');
    }
}
