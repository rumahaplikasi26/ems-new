# Server Filter Debug Guide

## 🔧 Masalah: Filter Bekerja di Lokal tapi Tidak di Server Deploy

### **Common Issues di Server Deploy:**
1. **JavaScript Dependencies** - Bootstrap datepicker tidak dimuat dengan benar
2. **Livewire Integration** - wire:model.live tidak bekerja dengan server
3. **Asset Loading** - JavaScript/CSS assets tidak accessible
4. **Browser Compatibility** - Browser di server berbeda dengan lokal

## ✅ Solusi yang Diimplementasikan

### **1. Replace Bootstrap Datepicker dengan HTML5 Date Inputs**

#### **Before (Problematic for Server):**
```html
<div class="input-daterange input-group" id="attendance-inputgroup"
    data-provide="datepicker" data-date-format="yyyy-mm-dd"
    data-date-container='#attendance-inputgroup' data-date-autoclose="true">
    <input type="text" class="form-control @error('start_date') is-invalid @enderror"
        wire:model.live="start_date" placeholder="{{ __('ems.start_date') }}" name="start" />
    <input type="text" class="form-control @error('end_date') is-invalid @enderror"
        wire:model.live="end_date" placeholder="{{ __('ems.end_date') }}" name="end" />
</div>
```

#### **After (Server Compatible):**
```html
<div class="input-daterange input-group" id="attendance-inputgroup">
    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
        wire:model="start_date" placeholder="{{ __('ems.start_date') }}" name="start" />
    <input type="date" class="form-control @error('end_date') is-invalid @enderror"
        wire:model="end_date" placeholder="{{ __('ems.end_date') }}" name="end" />
</div>
```

### **2. Simplified JavaScript Integration**

#### **Before (Complex Bootstrap Datepicker):**
```javascript
document.addEventListener('livewire:init', function() {
    initDatePicker();
    
    function initDatePicker() {
        $('#attendance-inputgroup').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
            inputs: $('#attendance-inputgroup').find('input')
        }).on('changeDate', function(e) {
            let startDate = $('#attendance-inputgroup').find('input[name="start"]').val();
            let endDate = $('#attendance-inputgroup').find('input[name="end"]').val();
            @this.set('start_date', startDate);
            @this.set('end_date', endDate);
        });
    }
});
```

#### **After (Simple Native Integration):**
```javascript
document.addEventListener('livewire:init', function() {
    // Add change event listeners for date inputs to ensure Livewire updates
    const startDateInput = document.querySelector('input[name="start"]');
    const endDateInput = document.querySelector('input[name="end"]');
    
    if (startDateInput) {
        startDateInput.addEventListener('change', function() {
            @this.set('start_date', this.value);
        });
    }
    
    if (endDateInput) {
        endDateInput.addEventListener('change', function() {
            @this.set('end_date', this.value);
        });
    }
});
```

### **3. Enhanced Filter Debugging**

#### **Added Debug Methods:**
```php
public function updatedSearch()
{
    $this->resetPage();
    // Debug for server deployment
    if (config('app.debug')) {
        \Log::info('Search updated', ['search' => $this->search]);
    }
}

public function updatedStart_date()
{
    $this->resetPage();
    // Debug for server deployment
    if (config('app.debug')) {
        \Log::info('Start date updated', ['start_date' => $this->start_date]);
    }
}

public function updatedEnd_date()
{
    $this->resetPage();
    // Debug for server deployment
    if (config('app.debug')) {
        \Log::info('End date updated', ['end_date' => $this->end_date]);
    }
}
```

#### **Added Render Debug:**
```php
public function render()
{
    // Debug filter values for server deployment
    if (config('app.debug')) {
        \Log::info('Filter values', [
            'search' => $this->search,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'perPage' => $this->perPage,
        ]);
    }
    
    // ... rest of render method
}
```

## 🔍 Troubleshooting Steps

### **1. Check Asset Loading**

#### **A. Check if Bootstrap Datepicker assets exist:**
```bash
# Check if these files exist on server:
public/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js
public/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css
```

#### **B. Check asset URLs:**
```bash
# Visit these URLs directly:
https://your-domain.com/libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js
https://your-domain.com/libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css
```

### **2. Check Browser Console**

#### **A. Open browser developer tools:**
```javascript
// Check for JavaScript errors in console
// Look for:
// - Failed to load resource errors
// - Livewire connection errors
// - Datepicker initialization errors
```

#### **B. Check Livewire connection:**
```javascript
// In browser console, check if Livewire is loaded:
console.log(window.Livewire);
console.log(window.Alpine);
```

### **3. Check Server Logs**

#### **A. Enable debug mode temporarily:**
```env
APP_DEBUG=true
```

#### **B. Check Laravel logs:**
```bash
tail -f storage/logs/laravel.log
```

#### **C. Look for filter debug messages:**
```
[timestamp] local.INFO: Search updated {"search":"john"}
[timestamp] local.INFO: Start date updated {"start_date":"2024-01-01"}
[timestamp] local.INFO: Filter values {"search":"john","start_date":"2024-01-01","end_date":"2024-01-31"}
```

### **4. Test Filter Functionality**

#### **A. Test search filter:**
```javascript
// In browser console, manually set search:
@this.set('search', 'test');
```

#### **B. Test date filter:**
```javascript
// In browser console, manually set dates:
@this.set('start_date', '2024-01-01');
@this.set('end_date', '2024-01-31');
```

## 🚀 Alternative Solutions

### **1. Force Filter Updates**

#### **If automatic updates don't work:**
```html
<!-- Add manual trigger buttons -->
<button type="button" class="btn btn-sm btn-primary" onclick="applyFilters()">
    Apply Filters
</button>

<script>
function applyFilters() {
    const search = document.querySelector('input[name="search"]').value;
    const startDate = document.querySelector('input[name="start"]').value;
    const endDate = document.querySelector('input[name="end"]').value;
    
    @this.set('search', search);
    @this.set('start_date', startDate);
    @this.set('end_date', endDate);
}
</script>
```

### **2. Use Form Submission**

#### **Alternative approach with form:**
```html
<form wire:submit.prevent="applyFilters">
    <input type="text" wire:model="search" placeholder="Search...">
    <input type="date" wire:model="start_date">
    <input type="date" wire:model="end_date">
    <button type="submit">Apply Filters</button>
</form>
```

```php
public function applyFilters()
{
    $this->resetPage();
    // Force re-render
    $this->render();
}
```

### **3. Check Livewire Configuration**

#### **A. Check Livewire config:**
```php
// config/livewire.php
'asset_url' => env('LIVEWIRE_ASSET_URL', null),
'asset_base_url' => env('LIVEWIRE_ASSET_BASE_URL', null),
```

#### **B. Check if Livewire assets are accessible:**
```bash
# Check Livewire assets:
https://your-domain.com/livewire/livewire.js
https://your-domain.com/livewire/livewire.js.map
```

## 🔧 Quick Fixes

### **1. Clear All Caches:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan livewire:discover
```

### **2. Reinstall Livewire:**
```bash
composer remove livewire/livewire
composer require livewire/livewire
php artisan livewire:publish --config
```

### **3. Check File Permissions:**
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod -R 755 public/
```

### **4. Check .env Configuration:**
```env
APP_URL=http://your-domain.com
APP_ENV=production
APP_DEBUG=false
ASSET_URL=https://your-domain.com
```

## 📊 Testing Checklist

### **Filter Functionality:**
- [ ] Search input works
- [ ] Start date input works
- [ ] End date input works
- [ ] Filter reset works
- [ ] Pagination resets when filter changes
- [ ] URL parameters are preserved

### **Browser Compatibility:**
- [ ] Chrome - All filters work
- [ ] Firefox - All filters work
- [ ] Safari - All filters work
- [ ] Edge - All filters work
- [ ] Mobile browsers - All filters work

### **Server Environment:**
- [ ] Assets load correctly
- [ ] JavaScript executes without errors
- [ ] Livewire connection established
- [ ] Filter values are logged
- [ ] Database queries execute correctly

## 🎯 Final Solution

### **If all else fails, use this robust implementation:**

#### **1. HTML with fallback:**
```html
<div class="row">
    <div class="col-md-4">
        <input type="text" wire:model="search" placeholder="Search..." class="form-control">
    </div>
    <div class="col-md-3">
        <input type="date" wire:model="start_date" class="form-control">
    </div>
    <div class="col-md-3">
        <input type="date" wire:model="end_date" class="form-control">
    </div>
    <div class="col-md-2">
        <button type="button" wire:click="applyFilters" class="btn btn-primary">
            Apply
        </button>
        <button type="button" wire:click="resetFilter" class="btn btn-secondary">
            Reset
        </button>
    </div>
</div>
```

#### **2. PHP with manual trigger:**
```php
public function applyFilters()
{
    $this->resetPage();
    // Force component refresh
    $this->dispatch('$refresh');
}

public function resetFilter()
{
    $this->reset(['search', 'start_date', 'end_date']);
    $this->resetPage();
    $this->dispatch('$refresh');
}
```

**Filter sekarang sudah dioptimalkan untuk server deployment!** 🚀✨

Implementasi ini menggunakan HTML5 native date inputs dan JavaScript yang lebih sederhana, sehingga lebih kompatibel dengan berbagai server environment.
