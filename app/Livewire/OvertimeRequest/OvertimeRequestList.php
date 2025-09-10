<?php

namespace App\Livewire\OvertimeRequest;

use App\Livewire\BaseComponent;
use App\Models\OvertimeRequest;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class OvertimeRequestList extends BaseComponent
{
    #[Reactive]
    public $overtime_requests;

    public function render()
    {
        return view('livewire.overtime-request.overtime-request-list');
    }
}
