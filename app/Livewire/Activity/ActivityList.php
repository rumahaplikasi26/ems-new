<?php

namespace App\Livewire\Activity;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class ActivityList extends Component
{
    #[Reactive]
    public $activities;

    public function render()
    {
        return view('livewire.activity.activity-list');
    }
}
