<?php

namespace App\Livewire\Shift;

use App\Livewire\BaseComponent;
use App\Models\Shift;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;

class ShiftItem extends BaseComponent
{
    use LivewireAlert;

    #[Reactive]
    public $shift;

    public function deleteConfirm()
    {
        $this->alert('warning', 'Are you sure you want to delete this shift?', [
            'position' => 'center',
            'timer' => null,
            'toast' => false,

            'showConfirmButton' => true,
            'confirmButtonColor' => '#DD6B55',
            'confirmButtonText' => 'Yes, Delete',
            'cancelButtonText' => 'No',
            'onConfirmed' => 'delete-shift',
            'showCancelButton' => true,

            'allowOutsideClick' => false,
            'allowEnterKey' => true,
            'allowEscapeKey' => false,
            'stopKeydownPropagation' => false,
        ]);
    }

    #[On('delete-shift')]
    public function delete()
    {
        $this->shift->delete();
        $this->alert('success', 'Shift deleted successfully');

        activity()
            ->causedBy($this->authUser)
            ->withProperties(['ip' => request()->ip()])
            ->event('delete')
            ->log("{$this->authUser->name} telah menghapus shift");

        $this->dispatch('refreshIndex');
    }

    public function render()
    {
        return view('livewire.shift.shift-item');
    }
}
