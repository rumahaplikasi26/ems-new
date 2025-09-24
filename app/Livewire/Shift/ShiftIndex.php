<?php

namespace App\Livewire\Shift;

use App\Models\Shift;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ShiftIndex extends Component
{
    use LivewireAlert, WithPagination;

    #[Url(except: '')]
    public $search;
    public $is_active = true;
    public $perPage = 10;
    public $showForm = true;

    protected $listeners = [
        'refreshIndex' => '$refresh',
    ];

    public function changeStatusForm()
    {
        if ($this->showForm) {
            $this->showForm = false;
        } else {
            $this->showForm = true;
        }
    }

    #[On('change-status-form')]
    public function updateShowForm()
    {
        $this->showForm = true;
        $this->dispatch('collapse-form');
    }

    public function render()
    {
        $shifts = Shift::when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%');
        })->where('is_active', $this->is_active)->orderBy('name', 'ASC');

        $shifts = $shifts->paginate($this->perPage);

        return view('livewire.shift.shift-index', compact('shifts'))->layout('layouts.app', ['title' => 'Shift Management']);
    }
}
