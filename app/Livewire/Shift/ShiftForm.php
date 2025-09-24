<?php

namespace App\Livewire\Shift;

use App\Livewire\BaseComponent;
use App\Models\Shift;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class ShiftForm extends BaseComponent
{
    use LivewireAlert;

    public $name, $start_time, $end_time, $description, $is_active = 1;
    public $shift_id;
    public $shift;
    public $statusForm = 'store';

    public function resetFormFields()
    {
        $this->name = null;
        $this->start_time = null;
        $this->end_time = null;
        $this->description = null;
        $this->is_active = 1;
        $this->statusForm = 'store';
        $this->dispatch('refreshIndex');
    }

    #[On('set-shift')]
    public function getDataShift($shift_id)
    {
        $this->shift = Shift::find($shift_id);
        $this->name = $this->shift->name;
        $this->start_time = $this->shift->start_time;
        $this->end_time = $this->shift->end_time;
        $this->description = $this->shift->description;
        $this->is_active = $this->shift->is_active;
        $this->statusForm = 'update';
        $this->dispatch('change-status-form');
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
            'description' => 'nullable|string|max:500',
        ]);

        try {
            if ($this->statusForm == 'store') {
                $shift = Shift::create([
                    'name' => $this->name,
                    'start_time' => $this->start_time,
                    'end_time' => $this->end_time,
                    'description' => $this->description,
                    'is_active' => $this->is_active,
                ]);

                $this->alert('success', 'Shift Created Successfully', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                ]);

                activity()
                    ->causedBy(auth()->user())
                    ->withProperties(['ip' => request()->ip()])
                    ->event('create')
                    ->log($this->authUser->name . ' telah membuat data shift');

                $this->dispatch('refreshIndex');
                $this->resetFormFields();
            } else {
                $this->shift->update([
                    'name' => $this->name,
                    'start_time' => $this->start_time,
                    'end_time' => $this->end_time,
                    'description' => $this->description,
                    'is_active' => $this->is_active,
                ]);

                $this->alert('success', 'Shift Updated Successfully', [
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                ]);

                activity()
                    ->causedBy(auth()->user())
                    ->withProperties(['ip' => request()->ip()])
                    ->event('update')
                    ->log($this->authUser->name . ' telah mengubah data shift');

                $this->dispatch('refreshIndex');
                $this->resetFormFields();
            }
        } catch (\Exception $e) {
            $this->alert('error', $e->getMessage(), [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.shift.shift-form');
    }
}
