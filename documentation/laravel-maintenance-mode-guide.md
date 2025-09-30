# Laravel Maintenance Mode Guide - EMS System

## Overview
Panduan sederhana untuk menggunakan Laravel's built-in maintenance mode dengan `php artisan down --with-secret` untuk sistem EMS.

## 🚀 Quick Usage

### **Enable Maintenance Mode:**
```bash
# Basic maintenance mode
php artisan down

# With custom message
php artisan down --message="EMS system maintenance in progress"

# With secret for developer access
php artisan down --secret="dev-access-2024"

# With custom message and secret
php artisan down --message="Database migration in progress" --secret="ems-dev-2024"

# Using our custom command
php artisan maintenance on --message="EMS maintenance" --secret="dev-2024"
```

### **Disable Maintenance Mode:**
```bash
# Disable maintenance
php artisan up

# Or using our custom command
php artisan maintenance off
```

### **Check Status:**
```bash
# Check if maintenance is active
php artisan maintenance status
```

## 🔧 Developer Access

### **Using Secret Token:**
```bash
# Enable with secret
php artisan down --secret="ems-dev-2024"

# Access with secret
https://yourdomain.com/ems-dev-2024
https://yourdomain.com?secret=ems-dev-2024
```

### **Using Custom Command:**
```bash
# Enable with auto-generated secret
php artisan maintenance on

# Output:
# ✅ EMS Maintenance mode enabled successfully!
# 📝 Message: The EMS system is currently under maintenance. Please try again later.
# 🔑 Secret: ems-a1b2c3d4e5f6g7h8
# 
# 🔧 Developer Access:
# URL: yourdomain.com/ems-a1b2c3d4e5f6g7h8
# Or: yourdomain.com?secret=ems-a1b2c3d4e5f6g7h8
```

## 📋 Command Examples

### **Basic Maintenance:**
```bash
# Simple maintenance
php artisan down

# With message
php artisan down --message="System update in progress"

# With secret
php artisan down --secret="dev-access"
```

### **Advanced Maintenance:**
```bash
# Full configuration
php artisan down \
  --message="Database migration in progress" \
  --secret="ems-migration-2024" \
  --render="maintenance"

# Using custom command
php artisan maintenance on \
  --message="HR system maintenance" \
  --secret="hr-dev-2024"
```

### **Emergency Maintenance:**
```bash
# Emergency maintenance
php artisan down --message="Emergency system maintenance" --secret="emergency-2024"

# Quick disable
php artisan up
```

## 🎯 Laravel Maintenance Mode Features

### **Built-in Features:**
- ✅ **503 Status Code** - Proper HTTP status
- ✅ **Secret Access** - Developer bypass with secret
- ✅ **Custom Message** - Configurable maintenance message
- ✅ **Custom Render** - Custom maintenance view
- ✅ **Automatic Bypass** - Admin users can access

### **Our Custom Command Features:**
- ✅ **Auto-generated Secrets** - Random secure secrets
- ✅ **EMS Branding** - Custom EMS messages
- ✅ **Status Check** - Check maintenance status
- ✅ **Easy Enable/Disable** - Simple commands

## 🔒 Security Features

### **Secret Access:**
```bash
# Generate secure secret
php artisan down --secret="ems-$(date +%s)"

# Use secret to access
https://yourdomain.com/ems-1640995200
```

### **Admin Bypass:**
- Admin users can still access the system during maintenance
- Super admin users have full access
- HR admin users can access (if configured)

## 📱 Response Types

### **Web Browser:**
```html
<h1>503 Service Unavailable</h1>
<p>The EMS system is currently under maintenance. Please try again later.</p>
```

### **JSON API:**
```json
{
    "error": "Service Unavailable",
    "message": "The EMS system is currently under maintenance. Please try again later.",
    "status": 503
}
```

## 🚨 Emergency Procedures

### **Emergency Enable:**
```bash
# Quick emergency maintenance
php artisan down --secret="emergency-$(date +%s)"

# Or using custom command
php artisan maintenance on --secret="emergency-2024"
```

### **Emergency Disable:**
```bash
# Quick disable
php artisan up

# Or using custom command
php artisan maintenance off
```

## 🔍 Troubleshooting

### **Common Issues:**

#### **1. Maintenance Not Working:**
```bash
# Check if maintenance is active
php artisan maintenance status

# Check Laravel maintenance
php artisan tinker
>>> app()->isDownForMaintenance()
```

#### **2. Secret Not Working:**
```bash
# Check current secret
php artisan maintenance status

# Try different URL formats
# https://yourdomain.com/secret
# https://yourdomain.com?secret=secret
```

#### **3. Admin Can't Access:**
```bash
# Check user role
php artisan tinker
>>> auth()->user()->hasRole(['admin', 'super_admin'])
```

## 📊 Monitoring

### **Check Maintenance Status:**
```bash
# Using custom command
php artisan maintenance status

# Direct Laravel check
php artisan tinker
>>> app()->isDownForMaintenance()
```

### **Log Maintenance Events:**
```php
// Add to middleware or service provider
Log::info('Maintenance mode accessed', [
    'secret' => $request->route('secret'),
    'ip' => $request->ip(),
    'user_agent' => $request->userAgent()
]);
```

## 🎯 Best Practices

### **1. Development:**
```bash
# Development maintenance
php artisan down --secret="dev-$(date +%s)"

# Local development
php artisan down --secret="local-dev"
```

### **2. Staging:**
```bash
# Staging maintenance
php artisan down --message="Staging server maintenance" --secret="staging-2024"
```

### **3. Production:**
```bash
# Production maintenance
php artisan down --message="Production server maintenance" --secret="prod-$(openssl rand -hex 8)"
```

## 📋 Deployment Workflow

### **Before Deployment:**
```bash
# Enable maintenance
php artisan down --secret="deploy-$(date +%s)"
```

### **During Deployment:**
```bash
# Run deployment commands
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **After Deployment:**
```bash
# Disable maintenance
php artisan up
```

## 🔧 Customization

### **Custom Maintenance View:**
```bash
# Use custom view
php artisan down --render="maintenance"

# Create custom view in resources/views/maintenance.blade.php
```

### **Custom Message:**
```bash
# Custom message
php artisan down --message="Employee Management System is currently under maintenance. We expect to be back online within 2 hours."
```

## 📞 Support

### **Emergency Contacts:**
- 🆘 **Emergency**: emergency@company.com | +62 812 9999 8888
- 💻 **DevOps**: devops@company.com | +62 812 8888 7777
- 🔧 **IT Support**: it@company.com | +62 812 7777 6666

### **Documentation:**
- 📖 **Laravel Docs**: [Laravel Maintenance Mode](https://laravel.com/docs/10.x/configuration#maintenance-mode)
- 🔧 **Custom Command**: `php artisan maintenance --help`

## 🎯 Summary

### **Simple Commands:**
```bash
# Enable maintenance
php artisan down --secret="your-secret"

# Disable maintenance
php artisan up

# Check status
php artisan maintenance status
```

### **Developer Access:**
```
# Access with secret
https://yourdomain.com/your-secret
https://yourdomain.com?secret=your-secret
```

Laravel maintenance mode sudah **sederhana, aman, dan efektif** untuk semua kebutuhan maintenance! 🚀
