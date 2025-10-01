<?php

namespace App\Livewire\Component;

use App\Livewire\Visit\VisitCreate;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class Map extends Component
{
    use LivewireAlert;

    #[Reactive]
    public $site_name, $site_longitude, $site_latitude;
    
    public $latitude, $longitude, $distance;

    public function mount()
    {
        // Trigger map refresh after component is mounted to initialize the map
        $this->dispatch('refresh-map', 
            latitude: $this->latitude ?? 0, 
            longitude: $this->longitude ?? 0, 
            site_latitude: $this->site_latitude ?? 0, 
            site_longitude: $this->site_longitude ?? 0, 
            site_name: $this->site_name ?? ''
        );
    }

    #[On('update-distance')]
    public function updateDistance($distance)
    {
        $this->distance = $distance;
    }

    #[On('update-coordinates')]
    public function updateCoordinates($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;

        $this->alert('success', 'Coordinates updated successfully');
        $this->dispatch('refresh-map', latitude: $this->latitude, longitude: $this->longitude, site_latitude: $this->site_latitude, site_longitude: $this->site_longitude, site_name: $this->site_name);
    }

    public function render()
    {
        return view('livewire.component.map');
    }
}
