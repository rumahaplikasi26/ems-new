<?php

namespace App\Livewire\Attendance;

use App\Models\Site;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use App\Livewire\BaseComponent;
use App\Models\Attendance;
use App\Models\AttendanceMethod;
use App\Models\AttendanceTemp;
use App\Models\Shift;
use App\Models\EmployeeShiftSchedule;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Features\SupportFileUploads\WithFileUploads;

class AttendanceCreate extends BaseComponent
{
    use LivewireAlert;
    public $attendance;
    public $employee_id, $machine_id, $attendance_method_id, $site_id, $shift_id, $timestamp, $longitude, $latitude, $notes, $image_path, $image_url, $image, $content, $distance;
    public $attendance_methods;
    public $sites;
    public $shifts;
    public $site_name = 'Office Site';
    public $site_latitude = '-6.2631219';
    public $site_longitude = '106.7988398';
    public $activeCamera = 'selfie';
    public $isScanError = false;

    protected $rules = [
        'employee_id' => 'required',
        'attendance_method_id' => 'required',
        'site_id' => 'required_if:attendance_method_id,2,3',
        'shift_id' => 'required',
        'notes' => 'required|max:255',
        'longitude' => 'required',
        'latitude' => 'required',
        'image' => 'required',
    ];

    public function resetSite()
    {
        $this->site_id = null;
        $this->site_name = null;
        $this->site_latitude = '-6.2631219';
        $this->site_longitude = '106.7988398';
    }

    public function updatedAttendanceMethodId()
    {
        if ($this->attendance_method_id == 2) {
            // Reset site selection for tag method
            $this->site_id = null;
            $this->site_name = 'Office Site';
            $this->site_latitude = '-6.2631219';
            $this->site_longitude = '106.7988398';
            
            // Dispatch event to reset Select2
            $this->dispatch('attendance-method-changed');
            
            // Dispatch event to initialize Select2 after DOM update
            $this->dispatch('init-select2');
        } elseif ($this->attendance_method_id == 3) {
            $this->activateQRScanner();
        } else {
            $this->site_id = null;
            $this->dispatch('attendance-method-changed');
        }
    }

    public function updatedSiteId()
    {
        if ($this->site_id) {
            $site = Site::find($this->site_id);
            if ($site) {
                $this->site_name = $site->name;
                $this->site_latitude = $site->latitude;
                $this->site_longitude = $site->longitude;
                
                // Update map with new site coordinates
                // Use current user coordinates if available, otherwise use site coordinates as fallback
                $userLat = $this->latitude ?: $this->site_latitude;
                $userLng = $this->longitude ?: $this->site_longitude;
                
                // Dispatch event to refresh map
                $this->dispatch('refresh-map', 
                    latitude: $userLat, 
                    longitude: $userLng, 
                    site_latitude: $this->site_latitude, 
                    site_longitude: $this->site_longitude, 
                    site_name: $this->site_name
                );
                
                // Also trigger a small delay to ensure the map component is ready
                $this->dispatch('$refresh');
            }
        }
    }

    #[On('image-captured')]
    public function imageCaptured($url)
    {
        $this->image = $url;
        if ($this->image) {
            $this->alert('success', 'Image captured successfully');
        }
    }

    #[On('qr-code-scanned')]
    public function qrCodeScanned($content)
    {
        try {
            $this->content = $content;

            $site = \App\Models\Site::where('uid', $content)->first();

            $this->site_id = $site->id;
            $this->site_name = $site->name;
            $this->site_latitude = $site->latitude;
            $this->site_longitude = $site->longitude;

            $this->alert('info', 'QR Code Scanned', [
                'position' => 'center',
                'showCancelButton' => true,
                'cancelButtonText' => 'Close',
                'toast' => false,
                'timer' => null,
                'html' => '<h3><strong>' . $site->name . '</strong></h3>' .
                    '<h4><strong>' . $site->longitude . ', ' . $site->latitude . '</strong></h4>',
            ]);

            $this->dispatch('refresh-map', latitude: $this->latitude, longitude: $this->longitude, site_latitude: $this->site_latitude, site_longitude: $this->site_longitude, site_name: $this->site_name);
        } catch (\Throwable $th) {
            $this->alert('error', 'Invalid QR Code');
            $this->content = null;
            $this->isScanError = true;
        }
    }

    public function mount()
    {
        $this->attendance_methods = \App\Models\AttendanceMethod::whereNot('name', 'Machine')->get();
        $this->sites = Site::all();
        $this->shifts = Shift::where('is_active', true)->get();
        $this->employee_id = $this->authUser->employee->id;
        
        // Set default shift based on employee's shift schedule for today or default shift
        $today = Carbon::today();
        $employeeShift = $this->authUser->employee->getShiftForDate($today);
        
        if ($employeeShift) {
            $this->shift_id = $employeeShift->id;
        } else {
            // Auto-detect shift based on current time
            $this->shift_id = $this->getCurrentShiftId();
        }
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
        
        // Only refresh map if we have a site selected
        if ($this->site_id) {
            $this->dispatch('refresh-map', 
                latitude: $this->latitude, 
                longitude: $this->longitude, 
                site_latitude: $this->site_latitude, 
                site_longitude: $this->site_longitude, 
                site_name: $this->site_name
            );
        }
    }

    public function submit()
    {
        try {
            $this->validate();
            $uid = Str::uuid();

            if ($this->image) {
                $imageData = $this->convertDataUrlToImage($this->image);
                // Store avatar in GCS using Laravel Storage
                $disk = Storage::disk('gcs');
                // Create ImageManager instance with GD driver
                $manager = new ImageManager(new Driver());

                // Read image from file system
                $image = $manager->read($imageData);

                // Resize image to create thumbnail
                $image->scale(250, 250); // Resize to fit thumbnail dimensions

                // Save the thumbnail to GCS
                $this->image_path = 'images/' . $uid . '.png';
                $disk->put($this->image_path, (string) $image->toPng());

                // Get the URL of the thumbnail
                $this->image_url = $disk->url($this->image_path);
            }

            if ($this->attendance_method_id == 3) {
                Attendance::create([
                    'employee_id' => $this->employee_id,
                    'attendance_method_id' => $this->attendance_method_id,
                    'site_id' => $this->site_id,
                    'shift_id' => $this->shift_id,
                    'timestamp' => Carbon::now()->format('Y-m-d H:i:s'),
                    'longitude' => $this->longitude,
                    'latitude' => $this->latitude,
                    'notes' => $this->notes,
                    'image_path' => $this->image_path,
                    'image_url' => $this->image_url,
                    'distance' => $this->distance,
                ]);

                $this->alert('success', 'Attendance created successfully');

                activity()
                    ->causedBy($this->authUser) // Pengguna yang melakukan login
                    ->withProperties(['ip' => request()->ip()]) // Menyimpan alamat IP
                    ->event('create')
                    ->log("{$this->authUser->name} telah membuat Attendance");

            } elseif ($this->attendance_method_id == 2) {
                AttendanceTemp::create([
                    'employee_id' => $this->employee_id,
                    'attendance_method_id' => $this->attendance_method_id,
                    'site_id' => $this->site_id,
                    'shift_id' => $this->shift_id,
                    'timestamp' => Carbon::now()->format('Y-m-d H:i:s'),
                    'longitude' => $this->longitude,
                    'latitude' => $this->latitude,
                    'notes' => $this->notes,
                    'image_path' => $this->image_path,
                    'image_url' => $this->image_url,
                    'distance' => $this->distance,
                ]);

                $this->alert('success', 'Attendance created successfully, please contact your HRD for approval');

                activity()
                    ->causedBy($this->authUser) // Pengguna yang melakukan login
                    ->withProperties(['ip' => request()->ip()]) // Menyimpan alamat IP
                    ->event('create')
                    ->log("{$this->authUser->name} telah membuat Attendance Temp");
            } else {
                $this->alert('warning', 'Attendance method not found');
            }

            $this->reset();
            return redirect()->route('attendance.index');
        } catch (\Throwable $th) {
            $this->alert('error', $th->getMessage());
        }
    }

    // Fungsi untuk mengkonversi data URL ke gambar
    private function convertDataUrlToImage($dataUrl)
    {
        $image_parts = explode(";base64,", $dataUrl);
        $image_base64 = base64_decode($image_parts[1]);

        return $image_base64;
    }

    public function activateQRScanner()
    {
        $this->dispatch('selfie-camera-stop')->to('component.camera');
        $this->dispatch('qr-scanner-start')->to('component.qr-camera');
        $this->activeCamera = 'qr';
    }

    public function activateSelfieCamera()
    {
        $this->dispatch('qr-scanner-stop')->to('component.qr-camera');
        $this->dispatch('selfie-camera-start')->to('component.camera');
        $this->activeCamera = 'selfie';
    }

    // Helper method to auto-detect current shift based on time
    private function getCurrentShiftId()
    {
        $currentTime = Carbon::now()->format('H:i:s');
        $shifts = Shift::where('is_active', true)->get();
        
        foreach ($shifts as $shift) {
            if ($shift->isWithinShift($currentTime)) {
                return $shift->id;
            }
        }
        
        // Default to first shift if no match found
        return $shifts->first()?->id;
    }

    public function render()
    {
        return view('livewire.attendance.attendance-create')->layout('layouts.app', ['title' => 'Attendance Create']);
    }
}
