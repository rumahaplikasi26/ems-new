# Livewire Tables Searchable Not Working on Deployment - Troubleshooting Guide

## Problem
Searchable functionality in Laravel Livewire Tables works on localhost but doesn't work on deployment server.

## Root Causes & Solutions

### 1. **Database Collation/Case Sensitivity Issue**

**Symptom**: Search works on Windows (localhost) but not on Linux (deployment)

**Cause**: MySQL on Windows uses case-insensitive collation by default, while Linux might use case-sensitive collation.

**Solution A: Update Query Method with Case-Insensitive Search**

```php
public function query(): Builder
{
    return AttendanceDetail::select('attendance_details.*')
        ->where('is_out_itinerary', true)
        ->with('attendance.employee', 'attendance.employee.position')
        ->whereHas('attendance', function ($query) {
            $query->where('date', $this->date);
        })
        // Add searchable relationships explicitly
        ->when($this->getAppliedSearchValue(), function($query, $search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('attendance.employee', function($employeeQuery) use ($search) {
                    $employeeQuery->where('nik', 'LIKE', '%' . $search . '%')
                                  ->orWhere('name', 'LIKE', '%' . $search . '%');
                })
                ->orWhereHas('attendance.employee.position', function($positionQuery) use ($search) {
                    $positionQuery->where('name', 'LIKE', '%' . $search . '%');
                });
            });
        })
        ->orderBy('attendance_details.id', 'desc');
}
```

**Solution B: Update Database Collation**

```sql
-- Check current collation
SHOW TABLE STATUS WHERE Name = 'attendance_details';

-- Update table collation to case-insensitive
ALTER TABLE attendance_details CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE attendances CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE employees CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE positions CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. **Relationship Search Not Working**

**Symptom**: Direct column search works but relationship column search doesn't work

**Cause**: Livewire Tables might not properly handle nested relationship searches on deployment.

**Solution: Use `searchableAs()` for Relationship Columns**

```php
public function columns(): array
{
    return [
        // ... other columns
        
        Column::make("NIK", "attendance.employee.nik")
            ->sortable()
            ->searchable()
            ->searchableAs('employees.nik'), // Explicitly define search path
            
        Column::make("Employee", "attendance.employee.name")
            ->sortable()
            ->searchable()
            ->searchableAs('employees.name'), // Explicitly define search path
            
        Column::make("Position", "attendance.employee.position.name")
            ->sortable()
            ->searchable()
            ->searchableAs('positions.name'), // Explicitly define search path
    ];
}
```

### 3. **Missing Joins for Searchable Relationships**

**Solution: Add Proper Joins in Query Method**

```php
public function query(): Builder
{
    return AttendanceDetail::select('attendance_details.*')
        ->where('is_out_itinerary', true)
        ->join('attendances', 'attendance_details.attendance_id', '=', 'attendances.id')
        ->join('employees', 'attendances.employee_id', '=', 'employees.id')
        ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
        ->with(['attendance.employee.position'])
        ->where('attendances.date', $this->date)
        ->orderBy('attendance_details.id', 'desc');
}
```

### 4. **Config Cache Issue**

**Symptom**: Config changes not applied on deployment

**Solution: Clear and Recache on Server**

```bash
# On deployment server
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Then recache (for production)
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. **Livewire Assets Not Published**

**Solution: Republish Livewire Assets**

```bash
# On deployment server
php artisan vendor:publish --tag=livewire:assets --force
php artisan vendor:publish --tag=livewire-tables --force

# Clear browser cache
```

### 6. **JavaScript/Alpine.js Issues**

**Solution: Check Browser Console for Errors**

1. Open browser console (F12)
2. Look for JavaScript errors
3. Check if Alpine.js is loaded properly
4. Verify Livewire scripts are loaded

```html
<!-- In your layout, ensure these are loaded -->
@livewireStyles
@livewireScripts
```

### 7. **Query Builder with whereHas Performance**

**Symptom**: Search times out on large datasets

**Solution: Use Joins Instead of whereHas**

```php
public function query(): Builder
{
    $query = AttendanceDetail::query()
        ->select([
            'attendance_details.*',
            'attendances.date',
            'employees.nik',
            'employees.name as employee_name',
            'positions.name as position_name'
        ])
        ->where('attendance_details.is_out_itinerary', true)
        ->join('attendances', 'attendance_details.attendance_id', '=', 'attendances.id')
        ->join('employees', 'attendances.employee_id', '=', 'employees.id')
        ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
        ->where('attendances.date', $this->date)
        ->orderBy('attendance_details.id', 'desc');
    
    return $query;
}
```

## Testing Steps

1. **Test on localhost**:
   ```bash
   php artisan serve
   # Try search functionality
   ```

2. **Test database collation**:
   ```bash
   php artisan tinker
   # Run: DB::select("SHOW TABLE STATUS WHERE Name = 'employees'");
   # Check Collation field
   ```

3. **Test on deployment**:
   ```bash
   ssh user@deployment-server
   cd /path/to/project
   php artisan config:clear
   php artisan cache:clear
   # Try search functionality on browser
   ```

4. **Check logs**:
   ```bash
   tail -f storage/logs/laravel.log
   # Look for SQL query errors
   ```

## Recommended Solution (Most Reliable)

Combine Solution A (explicit search handling) with proper joins:

```php
public function query(): Builder
{
    $query = AttendanceDetail::query()
        ->select('attendance_details.*')
        ->where('is_out_itinerary', true)
        ->join('attendances', 'attendance_details.attendance_id', '=', 'attendances.id')
        ->join('employees', 'attendances.employee_id', '=', 'employees.id')
        ->leftJoin('positions', 'employees.position_id', '=', 'positions.id')
        ->where('attendances.date', $this->date);

    // Handle search manually for better control
    if ($search = $this->getAppliedSearchValue()) {
        $query->where(function($q) use ($search) {
            $q->where('employees.nik', 'LIKE', "%{$search}%")
              ->orWhere('employees.name', 'LIKE', "%{$search}%")
              ->orWhere('positions.name', 'LIKE', "%{$search}%");
        });
    }

    return $query->orderBy('attendance_details.id', 'desc');
}
```

## Debug Helper

Add this to your component to debug search queries:

```php
public function updatedSearch($value)
{
    \Log::info('Search updated', [
        'value' => $value,
        'applied_search' => $this->getAppliedSearchValue(),
        'sql' => $this->query()->toSql(),
        'bindings' => $this->query()->getBindings()
    ]);
}
```

## Common Deployment Checklist

- [ ] Database collation is utf8mb4_unicode_ci (case-insensitive)
- [ ] All caches cleared after deployment
- [ ] Livewire assets published
- [ ] Browser cache cleared
- [ ] JavaScript console shows no errors
- [ ] Laravel logs show no SQL errors
- [ ] Query tested with joins instead of whereHas
- [ ] Search handled explicitly in query method

## Contact

If issue persists, check:
1. Laravel version compatibility with Livewire Tables
2. MySQL/MariaDB version differences between localhost and deployment
3. PHP version differences
4. Memory/timeout settings on deployment server
