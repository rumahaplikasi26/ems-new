# EMS Maintenance Mode - Panduan Lengkap

## Overview
Sistem maintenance mode khusus untuk Employee Management System (EMS-NEW) dengan desain yang sesuai dengan branding EMS dan fitur yang disesuaikan untuk sistem manajemen karyawan.

## 📁 Files yang Dibuat

### **1. HTML Pages**
- `public/maintenance.html` - Halaman maintenance mode lengkap dengan EMS branding
- `public/maintenance-simple.html` - Versi sederhana untuk backup

### **2. Laravel Integration**
- `app/Http/Middleware/MaintenanceMode.php` - Middleware khusus EMS
- `resources/views/maintenance.blade.php` - View Blade dengan EMS branding
- `app/Console/Commands/MaintenanceModeCommand.php` - Command Artisan untuk EMS

## 🎨 EMS Branding Features

### **1. Color Scheme EMS:**
- 🔵 **Primary Blue**: #1e3c72 (Deep blue)
- 🔵 **Secondary Blue**: #2a5298 (Medium blue)
- 🔵 **Accent Blue**: #3b82f6 (Light blue)
- ⚪ **Clean White**: #ffffff (Background)
- 🔘 **Text Gray**: #4a5568 (Main text)

### **2. EMS Logo Integration:**
```html
<div class="ems-logo">EMS</div>
```
- Modern gradient background
- Rounded corners
- Professional typography
- Consistent with EMS branding

### **3. EMS-Specific Content:**
- **Title**: "System Maintenance - EMS System"
- **Subtitle**: "Employee Management System maintenance"
- **Contact**: "ems-support@company.com"
- **System Name**: "Employee Management System"

## 🚀 Cara Penggunaan

### **1. Quick Setup (HTML Static):**

#### **Enable Maintenance Mode:**
```bash
# Backup index.php asli
cp public/index.php public/index.php.backup

# Ganti dengan EMS maintenance page
cp public/maintenance.html public/index.php
```

#### **Disable Maintenance Mode:**
```bash
# Restore index.php asli
cp public/index.php.backup public/index.php
```

### **2. Laravel Integration:**

#### **Register Middleware:**
Tambahkan di `app/Http/Kernel.php`:
```php
protected $middleware = [
    // ... existing middleware
    \App\Http\Middleware\MaintenanceMode::class,
];
```

#### **Enable EMS Maintenance Mode:**
```bash
# Basic enable
php artisan maintenance on

# With custom message
php artisan maintenance on --message="Employee database optimization in progress"

# With custom ETA
php artisan maintenance on --eta="4 hours"

# With custom contact info
php artisan maintenance on --email="hr@company.com" --phone="+62 812 3456 7890"

# Full custom for EMS
php artisan maintenance on \
  --message="Employee Management System database migration in progress" \
  --eta="6 hours" \
  --email="ems-support@company.com" \
  --phone="+62 21 1234 5678"
```

#### **Disable Maintenance Mode:**
```bash
php artisan maintenance off
```

#### **Check Status:**
```bash
php artisan maintenance status
```

## 🎯 EMS-Specific Features

### **1. Employee-Focused Messaging:**
```php
// Default EMS messages
'We are currently performing scheduled maintenance on the Employee Management System.'
'Our team is working hard to enhance the EMS performance and add new features.'
'Employee Management System database migration in progress'
```

### **2. HR-Contact Integration:**
```php
// EMS-specific contact information
'ems-support@company.com'
'HR Support: +62 21 1234 5678'
'WhatsApp: +62 812 3456 7890'
```

### **3. Role-Based Access:**
```php
// Allow HR and Admin roles during maintenance
if (auth()->check() && auth()->user()->hasRole(['super_admin', 'admin', 'hr'])) {
    return $next($request);
}
```

### **4. Employee Login Access:**
```php
// Allow login attempts during maintenance
if ($request->is('login') || $request->is('auth/login')) {
    return $next($request);
}
```

## 📋 Command Examples

### **HR Maintenance Scenarios:**
```bash
# Employee database backup
php artisan maintenance on --message="Employee database backup in progress" --eta="2 hours"

# Payroll system update
php artisan maintenance on --message="Payroll system update in progress" --eta="4 hours" --email="payroll@company.com"

# Performance review system upgrade
php artisan maintenance on --message="Performance review system upgrade in progress" --eta="6 hours" --email="hr@company.com"

# Employee portal maintenance
php artisan maintenance on --message="Employee self-service portal maintenance" --eta="3 hours"
```

### **Emergency Maintenance:**
```bash
# Emergency database fix
php artisan maintenance on --message="Emergency database maintenance - Employee data integrity check" --eta="1-2 hours" --email="it-emergency@company.com" --phone="+62 812 9999 8888"
```

## 🔧 Configuration

### **Environment Variables:**
```env
# EMS Maintenance Mode Settings
MAINTENANCE_MODE=false
MAINTENANCE_MESSAGE="We are currently performing scheduled maintenance on the Employee Management System."
MAINTENANCE_ETA="2-3 hours"
MAINTENANCE_CONTACT_EMAIL="ems-support@company.com"
MAINTENANCE_CONTACT_PHONE="+62 21 1234 5678"
```

### **Middleware Configuration:**
```php
// In app/Http/Middleware/MaintenanceMode.php
protected $allowedRoutes = [
    'maintenance',
    'admin.maintenance',
    'api.health',
    'login',
    'logout'
];

// Allow HR and Admin roles
if (auth()->check() && auth()->user()->hasRole(['super_admin', 'admin', 'hr'])) {
    return $next($request);
}
```

## 🎨 Customization

### **1. EMS Logo Customization:**
```css
.ems-logo {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #your-brand-color1, #your-brand-color2);
    border-radius: 20px;
    /* Add your custom logo styling */
}
```

### **2. Company-Specific Content:**
```html
<!-- Change company name -->
<div class="ems-logo">YOUR COMPANY</div>

<!-- Change contact info -->
<div class="contact-item">
    <i class="fas fa-phone"></i>
    <span>Your Company Phone</span>
</div>
```

### **3. Department-Specific Maintenance:**
```bash
# HR Department maintenance
php artisan maintenance on --message="HR Department system maintenance" --email="hr@yourcompany.com"

# IT Department maintenance
php artisan maintenance on --message="IT infrastructure maintenance" --email="it@yourcompany.com"

# Payroll Department maintenance
php artisan maintenance on --message="Payroll system maintenance" --email="payroll@yourcompany.com"
```

## 📱 EMS Mobile Features

### **Employee Mobile Access:**
- ✅ **Mobile-Optimized** - Touch-friendly for employee self-service
- ✅ **Quick Contact** - Easy access to HR support
- ✅ **Status Updates** - Real-time maintenance progress
- ✅ **Emergency Contact** - Direct line to HR emergency

### **HR Mobile Management:**
- ✅ **Admin Bypass** - HR can access during maintenance
- ✅ **Emergency Access** - Critical HR functions remain available
- ✅ **Status Monitoring** - Track maintenance progress

## 🔒 EMS Security Features

### **1. Role-Based Access:**
```php
// Allow specific roles during maintenance
$allowedRoles = ['super_admin', 'admin', 'hr', 'payroll_admin'];
if (auth()->check() && auth()->user()->hasAnyRole($allowedRoles)) {
    return $next($request);
}
```

### **2. Department Isolation:**
```php
// Allow specific departments
if (auth()->check() && auth()->user()->department === 'IT') {
    return $next($request);
}
```

### **3. Emergency Override:**
```php
// Allow emergency access for critical HR functions
if ($request->is('emergency/*') && auth()->check()) {
    return $next($request);
}
```

## 📊 EMS Monitoring

### **1. Employee Impact Tracking:**
```php
// Log maintenance access attempts
Log::info('EMS Maintenance access', [
    'employee_id' => auth()->id(),
    'department' => auth()->user()->department,
    'ip' => $request->ip(),
    'timestamp' => now()
]);
```

### **2. HR Dashboard Integration:**
```php
// Track maintenance duration
Cache::put('ems_maintenance_start', now());
Cache::put('ems_maintenance_impact_count', 0);
```

## 🚀 EMS Deployment

### **1. HR System Updates:**
```bash
# Enable maintenance before HR system update
php artisan maintenance on --message="HR system update in progress" --eta="2 hours" --email="hr@company.com"

# Update HR modules
composer update
php artisan migrate --force
php artisan config:cache

# Disable maintenance
php artisan maintenance off
```

### **2. Employee Database Migration:**
```bash
# Enable maintenance for database migration
php artisan maintenance on --message="Employee database migration in progress" --eta="4-6 hours"

# Run database migrations
php artisan migrate --force

# Disable maintenance
php artisan maintenance off
```

## 🎯 EMS Best Practices

### **1. HR Communication:**
- ✅ **Advance Notice** - Notify employees 24 hours before
- ✅ **Department Heads** - Inform managers first
- ✅ **Alternative Access** - Provide backup contact methods
- ✅ **Regular Updates** - Keep employees informed

### **2. Timing:**
- ✅ **Off-Peak Hours** - Schedule during low employee usage
- ✅ **Weekend Maintenance** - Use weekends for major updates
- ✅ **Holiday Periods** - Plan during company holidays
- ✅ **Shift Changes** - Avoid during shift change times

### **3. Emergency Procedures:**
- ✅ **Emergency Contact** - Provide 24/7 IT support number
- ✅ **Escalation Path** - Clear escalation procedures
- ✅ **Backup Systems** - Have backup access methods
- ✅ **Communication Plan** - Emergency communication protocols

## 🔧 EMS Troubleshooting

### **Common EMS Issues:**

#### **1. Employee Can't Access:**
```bash
# Check maintenance status
php artisan maintenance status

# Check employee role permissions
php artisan tinker
>>> auth()->user()->hasRole('employee')
```

#### **2. HR Can't Access:**
```bash
# Check HR role permissions
php artisan tinker
>>> auth()->user()->hasRole(['admin', 'hr'])
```

#### **3. Payroll System Down:**
```bash
# Emergency payroll access
php artisan maintenance on --message="Payroll system emergency maintenance" --email="payroll@company.com" --phone="+62 812 9999 8888"
```

## 📞 EMS Support Contacts

### **Department Contacts:**
- 🏢 **HR Department**: hr@company.com | +62 21 1234 5678
- 💰 **Payroll Department**: payroll@company.com | +62 21 1234 5679
- 💻 **IT Department**: it@company.com | +62 21 1234 5680
- 🆘 **Emergency Support**: emergency@company.com | +62 812 9999 8888

### **Employee Self-Service:**
- 📱 **WhatsApp Support**: +62 812 3456 7890
- 🌐 **Employee Portal**: [Link to employee portal]
- 📧 **General Support**: support@company.com

## 🎯 Perfect for EMS:

### **HR Operations:**
- ✅ **Employee Database Maintenance** - Safe database updates
- ✅ **Payroll System Updates** - Secure payroll processing
- ✅ **Performance Review System** - Annual review system updates
- ✅ **Benefits Administration** - Benefits system maintenance

### **IT Operations:**
- ✅ **System Upgrades** - EMS version updates
- ✅ **Security Patches** - Security system updates
- ✅ **Database Optimization** - Performance improvements
- ✅ **Backup Procedures** - Data backup operations

EMS Maintenance mode system sudah **lengkap dan disesuaikan khusus untuk Employee Management System**! 🚀👥
