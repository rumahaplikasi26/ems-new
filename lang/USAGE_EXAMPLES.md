# Contoh Penggunaan Bahasa Indonesia di EMS

Dokumentasi ini berisi contoh-contoh penggunaan sistem bahasa Indonesia yang telah diimplementasikan.

## 1. Penggunaan di Blade Views

### Dashboard
```blade
{{-- resources/views/livewire/dashboard/dashboard-index.blade.php --}}
<div>
    @livewire('component.page.breadcrumb', ['breadcrumbs' => [['name' => __('ems.dashboard'), 'url' => '/']]])
    
    <div class="row">
        <div class="col-md-3">
            @livewire('component.card-mini', [
                'title' => __('ems.total_daily_report'),
                'value' => $totalDailyReport,
                'badge' => __('ems.monthly'),
            ])
        </div>
    </div>
</div>
```

### Employee Management
```blade
{{-- resources/views/livewire/employee/employee-index.blade.php --}}
<div>
    @livewire('component.page.breadcrumb', ['breadcrumbs' => [
        ['name' => __('ems.application'), 'url' => '/'], 
        ['name' => __('ems.employee'), 'url' => route('employee.index')]
    ]])
    
    <div class="card">
        <div class="card-body">
            <input type="search" class="form-control" placeholder="{{ __('ems.search_for') }}">
            <button class="btn btn-primary">{{ __('ems.create') }}</button>
            <button class="btn btn-warning">{{ __('ems.reset_filter') }}</button>
        </div>
    </div>
</div>
```

### Attendance Form
```blade
{{-- resources/views/livewire/attendance/attendance-create.blade.php --}}
<div>
    <h4 class="card-title mb-4">{{ __('ems.attendance') }}</h4>
    
    <form wire:submit.prevent="submit">
        <div class="mb-3">
            <label for="attendance_method_id" class="form-label">{{ __('ems.attendance_method') }}</label>
            <!-- form fields -->
        </div>
        
        <div class="mb-3">
            <label for="notes">{{ __('ems.notes') }}</label>
            <textarea class="form-control" wire:model="notes"></textarea>
        </div>
        
        <div class="mb-3 d-flex justify-content-end gap-2">
            <button type="submit" class="btn btn-primary">
                {{ __('ems.save') }}
            </button>
            <button type="button" class="btn btn-light">
                {{ __('ems.cancel') }}
            </button>
        </div>
    </form>
</div>
```

## 2. Penggunaan di Livewire Components

### Component dengan Translation
```php
<?php
// app/Livewire/Employee/EmployeeIndex.php

namespace App\Livewire\Employee;

use Livewire\Component;

class EmployeeIndex extends Component
{
    public function render()
    {
        return view('livewire.employee.employee-index', [
            'breadcrumbs' => [
                ['name' => __('ems.application'), 'url' => '/'],
                ['name' => __('ems.employee'), 'url' => route('employee.index')]
            ]
        ]);
    }
    
    public function create()
    {
        session()->flash('success', __('messages.success.created'));
        return redirect()->route('employee.create');
    }
    
    public function delete($id)
    {
        // Delete logic
        session()->flash('success', __('messages.success.deleted'));
    }
}
```

### Form Validation Messages
```php
<?php
// app/Livewire/Employee/EmployeeForm.php

namespace App\Livewire\Employee;

use Livewire\Component;

class EmployeeForm extends Component
{
    public $name;
    public $email;
    public $phone;
    
    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:employees',
        'phone' => 'required|string|max:20',
    ];
    
    protected $messages = [
        'name.required' => 'Nama wajib diisi.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah digunakan.',
        'phone.required' => 'Nomor telepon wajib diisi.',
    ];
    
    public function save()
    {
        $this->validate();
        
        // Save logic
        session()->flash('success', __('messages.success.saved'));
    }
}
```

## 3. Penggunaan di Controllers

### Controller dengan Translation
```php
<?php
// app/Http/Controllers/EmployeeController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('employee.index', [
            'title' => __('ems.employee'),
            'breadcrumbs' => [
                ['name' => __('ems.application'), 'url' => '/'],
                ['name' => __('ems.employee'), 'url' => route('employee.index')]
            ]
        ]);
    }
    
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees',
        ]);
        
        // Store logic
        return redirect()->route('employee.index')
            ->with('success', __('messages.success.created'));
    }
    
    public function destroy($id)
    {
        // Delete logic
        return redirect()->route('employee.index')
            ->with('success', __('messages.success.deleted'));
    }
}
```

## 4. Penggunaan di Email Templates

### Email Template dengan Translation
```blade
{{-- resources/views/emails/welcome.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <title>{{ __('email.subjects.welcome') }}</title>
</head>
<body>
    <h1>{{ __('email.templates.welcome.title') }}</h1>
    
    <p>{{ __('email.greetings.hello') }} {{ $user->name }},</p>
    
    <p>{{ __('email.templates.welcome.content') }}</p>
    
    <a href="{{ $actionUrl }}" class="btn btn-primary">
        {{ __('email.templates.welcome.action') }}
    </a>
    
    <p>{{ __('email.common.thank_you_for_using') }}</p>
    
    <p>{{ __('email.closings.best_regards') }},<br>
    {{ __('email.footer.company_name') }}</p>
</body>
</html>
```

## 5. Penggunaan di JavaScript

### JavaScript dengan Translation
```javascript
// resources/js/app.js

// Function untuk menampilkan pesan sukses
function showSuccessMessage(message) {
    // Menggunakan pesan dari Laravel
    const successMessage = message || '{{ __("messages.success.operation_successful") }}';
    
    // Tampilkan notifikasi
    showNotification(successMessage, 'success');
}

// Function untuk konfirmasi hapus
function confirmDelete(itemName) {
    const message = `{{ __("messages.confirm.delete") }}`;
    return confirm(message);
}

// Function untuk loading
function showLoading() {
    const message = '{{ __("messages.info.loading") }}';
    showSpinner(message);
}
```

## 6. Penggunaan di API Responses

### API dengan Translation
```php
<?php
// app/Http/Controllers/Api/EmployeeController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all();
        
        return response()->json([
            'success' => true,
            'message' => __('messages.success.data_loaded'),
            'data' => $employees
        ]);
    }
    
    public function store(Request $request)
    {
        $employee = Employee::create($request->all());
        
        return response()->json([
            'success' => true,
            'message' => __('messages.success.created'),
            'data' => $employee
        ], 201);
    }
    
    public function destroy($id)
    {
        Employee::findOrFail($id)->delete();
        
        return response()->json([
            'success' => true,
            'message' => __('messages.success.deleted')
        ]);
    }
}
```

## 7. Penggunaan di Notifications

### Notification dengan Translation
```php
<?php
// app/Notifications/EmployeeCreated.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EmployeeCreated extends Notification
{
    use Queueable;
    
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }
    
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(__('email.subjects.employee_created'))
            ->greeting(__('email.greetings.hello'))
            ->line(__('email.templates.employee_created.content'))
            ->action(__('email.templates.employee_created.action'), url('/'))
            ->line(__('email.common.thank_you_for_using'));
    }
    
    public function toDatabase($notifiable)
    {
        return [
            'title' => __('ems.employee'),
            'message' => __('messages.success.created'),
            'type' => 'success'
        ];
    }
}
```

## 8. Penggunaan di Commands

### Artisan Command dengan Translation
```php
<?php
// app/Console/Commands/ImportEmployees.php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ImportEmployees extends Command
{
    protected $signature = 'employees:import {file}';
    protected $description = 'Import employees from file';
    
    public function handle()
    {
        $this->info(__('messages.info.processing'));
        
        try {
            // Import logic
            $this->info(__('messages.success.imported'));
        } catch (\Exception $e) {
            $this->error(__('messages.error.import_failed'));
        }
    }
}
```

## 9. Penggunaan di Middleware

### Middleware dengan Translation
```php
<?php
// app/Http/Middleware/CheckPermission.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!auth()->user()->can($permission)) {
            abort(403, __('messages.error.unauthorized'));
        }
        
        return $next($request);
    }
}
```

## 10. Penggunaan di Tests

### Test dengan Translation
```php
<?php
// tests/Feature/EmployeeTest.php

namespace Tests\Feature;

use Tests\TestCase;

class EmployeeTest extends TestCase
{
    public function test_employee_can_be_created()
    {
        $response = $this->post('/employees', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success', __('messages.success.created'));
    }
    
    public function test_employee_validation()
    {
        $response = $this->post('/employees', []);
        
        $response->assertSessionHasErrors([
            'name' => __('validation.required', ['attribute' => 'name']),
            'email' => __('validation.required', ['attribute' => 'email']),
        ]);
    }
}
```

## Tips dan Best Practices

1. **Selalu gunakan fungsi `__()`** untuk teks yang akan ditampilkan ke user
2. **Jangan hardcode teks** dalam bahasa tertentu
3. **Gunakan key yang deskriptif** dan konsisten
4. **Kelompokkan teks** berdasarkan konteks penggunaan
5. **Test dengan bahasa yang berbeda** untuk memastikan UI tidak rusak
6. **Gunakan fallback** ke bahasa Inggris jika terjemahan tidak tersedia
7. **Update dokumentasi** ketika menambah teks baru
