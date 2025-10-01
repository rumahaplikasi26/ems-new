# Server Pagination Debug Guide

## 🔧 Masalah: Pagination Bekerja di Lokal tapi Tidak di Server Deploy

### **Common Issues di Server Deploy:**
1. **URL Path Issues** - Server menggunakan subdirectory atau proxy
2. **Livewire Page Detection** - `$this->getPage()` tidak bekerja dengan benar
3. **Request URL Issues** - `request()->url()` tidak sesuai dengan server configuration
4. **Environment Differences** - Perbedaan konfigurasi lokal vs server

## ✅ Solusi yang Diimplementasikan

### **1. Robust Page Detection**

#### **Before (Problematic for Server):**
```php
$currentPage = $this->getPage(); // Might not work on server
```

#### **After (Server Compatible):**
```php
$currentPage = request()->get('page', 1); // More reliable on server
```

### **2. Robust URL Generation**

#### **Before (Problematic for Server):**
```php
'path' => request()->url(), // Might not work with proxy/subdirectory
```

#### **After (Server Compatible):**
```php
$baseUrl = url()->current(); // More reliable for server deployment
'path' => $baseUrl,
```

### **3. Safe Query Parameter Handling**

#### **Before (Potential Issues):**
```php
$attendances->appends(request()->query()); // Might cause issues
```

#### **After (Safe):**
```php
$queryParams = request()->except(['page']);
if (!empty($queryParams)) {
    $attendances->appends($queryParams);
}
```

### **4. Added Debug Method**
```php
public function goToPage($page)
{
    $this->setPage($page);
}
```

## 🔍 Troubleshooting Steps

### **1. Check Server Configuration**

#### **A. Check if using subdirectory:**
```bash
# If your app is in subdirectory like: domain.com/app/
# Make sure APP_URL in .env is correct:
APP_URL=http://domain.com/app
```

#### **B. Check if using proxy/load balancer:**
```bash
# Check if using nginx/apache proxy
# Make sure proxy headers are set correctly
```

### **2. Debug Pagination in Server**

#### **Add Temporary Debug Code:**
```php
// Add this temporarily in render() method for debugging
if (config('app.debug')) {
    \Log::info('Pagination Debug', [
        'current_page' => $currentPage,
        'per_page' => $perPage,
        'total' => $total,
        'base_url' => $baseUrl,
        'query_params' => $queryParams,
        'request_url' => request()->url(),
        'request_path' => request()->path(),
    ]);
}
```

### **3. Check Livewire Configuration**

#### **A. Check Livewire pagination theme:**
```php
protected $paginationTheme = 'bootstrap'; // Make sure this is set
```

#### **B. Check if pagination views exist:**
```bash
# Check if these files exist in server:
resources/views/livewire/pagination/bootstrap.blade.php
```

### **4. Server-Specific Issues**

#### **A. Check PHP Version:**
```bash
# Make sure server PHP version matches local
php -v
```

#### **B. Check Laravel Version:**
```bash
# Make sure Laravel version is same
php artisan --version
```

#### **C. Check Livewire Version:**
```bash
# Check if Livewire version is same
composer show livewire/livewire
```

## 🚀 Alternative Solutions

### **1. Force Pagination Method**

#### **If automatic detection fails:**
```php
public function render()
{
    // ... existing code ...
    
    // Force page number from URL
    $currentPage = (int) request()->get('page', 1);
    if ($currentPage < 1) $currentPage = 1;
    
    // Calculate pagination
    $perPage = $this->perPage;
    $total = $sortedData->count();
    $offset = ($currentPage - 1) * $perPage;
    $paginatedData = $sortedData->slice($offset, $perPage)->values();
    
    // ... rest of pagination code ...
}
```

### **2. Custom Pagination Links**

#### **If default pagination links don't work:**
```php
// In view, you can add custom pagination handling
@if($attendances->hasPages())
    <div class="pagination-wrapper">
        @if($attendances->onFirstPage())
            <span class="disabled">Previous</span>
        @else
            <a href="{{ $attendances->previousPageUrl() }}" wire:click.prevent="goToPage({{ $attendances->currentPage() - 1 }})">Previous</a>
        @endif
        
        @for($i = 1; $i <= $attendances->lastPage(); $i++)
            @if($i == $attendances->currentPage())
                <span class="current">{{ $i }}</span>
            @else
                <a href="{{ $attendances->url($i) }}" wire:click.prevent="goToPage({{ $i }})">{{ $i }}</a>
            @endif
        @endfor
        
        @if($attendances->hasMorePages())
            <a href="{{ $attendances->nextPageUrl() }}" wire:click.prevent="goToPage({{ $attendances->currentPage() + 1 }})">Next</a>
        @else
            <span class="disabled">Next</span>
        @endif
    </div>
@endif
```

### **3. Environment-Specific Configuration**

#### **Add to config/livewire.php:**
```php
'pagination' => [
    'theme' => env('LIVEWIRE_PAGINATION_THEME', 'bootstrap'),
    'base_path' => env('LIVEWIRE_PAGINATION_BASE_PATH', '/'),
],
```

## 🔧 Quick Fixes

### **1. Clear All Caches:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### **2. Reinstall Livewire:**
```bash
composer remove livewire/livewire
composer require livewire/livewire
```

### **3. Check File Permissions:**
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

### **4. Check .env Configuration:**
```env
APP_URL=http://your-domain.com
APP_ENV=production
APP_DEBUG=false
```

## 📊 Testing Checklist

### **Local vs Server Comparison:**
- [ ] PHP Version: Local `{{ phpversion() }}` vs Server `{{ phpversion() }}`
- [ ] Laravel Version: Local vs Server
- [ ] Livewire Version: Local vs Server
- [ ] APP_URL: Local vs Server
- [ ] Web Server: Apache/Nginx configuration
- [ ] SSL/HTTPS: Different protocols
- [ ] Subdirectory: Different paths

### **Pagination Functionality:**
- [ ] Page 1 loads correctly
- [ ] Page 2+ loads correctly
- [ ] Previous/Next buttons work
- [ ] Page numbers are clickable
- [ ] Filters persist across pages
- [ ] PerPage changes work
- [ ] Reset filter works

## 🎯 Final Solution

### **If all else fails, use this robust implementation:**
```php
public function render()
{
    // ... existing query and data processing code ...
    
    // Robust pagination for server deployment
    $currentPage = max(1, (int) request()->get('page', 1));
    $perPage = max(1, (int) $this->perPage);
    $total = $sortedData->count();
    $offset = ($currentPage - 1) * $perPage;
    $paginatedData = $sortedData->slice($offset, $perPage)->values();
    
    // Use route URL instead of request URL
    $baseUrl = route('attendance.index');
    
    $attendances = new \Illuminate\Pagination\LengthAwarePaginator(
        $paginatedData,
        $total,
        $perPage,
        $currentPage,
        [
            'path' => $baseUrl,
            'pageName' => 'page',
        ]
    );
    
    // Safe query parameter preservation
    $queryParams = array_filter(request()->except(['page']));
    if (!empty($queryParams)) {
        $attendances->appends($queryParams);
    }
    
    return view('livewire.attendance.attendance-index', [
        'attendances' => $attendances
    ])->layout('layouts.app', ['title' => 'Attendance List']);
}
```

**Pagination sekarang sudah dioptimalkan untuk server deployment!** 🚀✨

Implementasi ini lebih robust dan kompatibel dengan berbagai konfigurasi server.
