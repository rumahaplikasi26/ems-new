# Developer Access Guide - EMS Maintenance Mode

## Overview
Panduan lengkap untuk developer agar bisa mengakses sistem EMS saat maintenance mode aktif. Ada 3 metode akses yang tersedia untuk memastikan developer tetap bisa bekerja saat maintenance.

## 🔧 Metode Akses Developer

### **1. Dev Token Access**
Menggunakan token khusus yang bisa ditambahkan ke URL untuk bypass maintenance mode.

#### **Cara Menggunakan:**
```bash
# Generate token saat enable maintenance
php artisan maintenance on --dev-token="my_secret_token"

# Akses dengan token
https://yourdomain.com?dev_token=my_secret_token
https://yourdomain.com/dashboard?dev_token=my_secret_token
https://yourdomain.com/admin?dev_token=my_secret_token
```

#### **Auto-Generate Token:**
```bash
# Token akan di-generate otomatis jika tidak di-specify
php artisan maintenance on
# Output: 🔑 Dev Token: dev_a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6
```

### **2. IP Whitelist Access**
Developer bisa mengakses dari IP address yang sudah di-whitelist.

#### **Setup IP Whitelist:**
```bash
# Single IP
php artisan maintenance on --dev-ips="192.168.1.100"

# Multiple IPs
php artisan maintenance on --dev-ips="192.168.1.100,192.168.1.101,10.0.0.50"

# Include localhost
php artisan maintenance on --dev-ips="127.0.0.1,::1,192.168.1.100"

# Office IP range
php artisan maintenance on --dev-ips="192.168.1.0/24,10.0.0.0/24"
```

#### **Check Current IP:**
```bash
# Check your current IP
curl ifconfig.me
# atau
wget -qO- ifconfig.me
```

### **3. Admin Role Access**
Login sebagai user dengan role admin atau super_admin.

#### **Roles yang Diizinkan:**
- `super_admin` - Super administrator
- `admin` - Administrator
- `hr` - HR administrator (opsional)

#### **Cara Login:**
```bash
# Login normal saat maintenance
https://yourdomain.com/login
# Masukkan credentials admin
```

## 🚀 Command Examples

### **Basic Developer Setup:**
```bash
# Enable maintenance dengan auto-generated dev token
php artisan maintenance on

# Enable maintenance dengan custom token
php artisan maintenance on --dev-token="dev_ems_2024"

# Enable maintenance dengan IP whitelist
php artisan maintenance on --dev-ips="192.168.1.100,192.168.1.101"
```

### **Full Developer Configuration:**
```bash
# Complete setup untuk development team
php artisan maintenance on \
  --message="Development server maintenance" \
  --eta="1 hour" \
  --email="dev@company.com" \
  --phone="+62 812 3456 7890" \
  --dev-token="dev_team_2024" \
  --dev-ips="192.168.1.100,192.168.1.101,10.0.0.50"
```

### **Check Developer Access:**
```bash
# Check maintenance status dan dev access
php artisan maintenance status

# Output example:
# 🔧 EMS Maintenance mode is ENABLED
# 🔑 Dev Token: dev_a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6
# 🌐 Dev IPs: 192.168.1.100,192.168.1.101
# 
# 🔧 Developer Access:
# URL: yourdomain.com?dev_token=dev_a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6
# IPs: 192.168.1.100,192.168.1.101
```

## 🔧 Environment Configuration

### **Manual .env Setup:**
```env
# Maintenance Mode Settings
MAINTENANCE_MODE=true
MAINTENANCE_MESSAGE="Development server maintenance"
MAINTENANCE_ETA="1 hour"
MAINTENANCE_CONTACT_EMAIL="dev@company.com"
MAINTENANCE_CONTACT_PHONE="+62 812 3456 7890"

# Developer Access Settings
MAINTENANCE_DEV_TOKEN="dev_team_2024"
MAINTENANCE_DEV_IPS="192.168.1.100,192.168.1.101,10.0.0.50"
```

### **Config Cache:**
```bash
# Clear config cache setelah update .env
php artisan config:clear
php artisan config:cache
```

## 🎯 Developer Workflow

### **1. Development Environment:**
```bash
# Enable maintenance untuk development
php artisan maintenance on --dev-token="dev_local" --dev-ips="127.0.0.1,::1"

# Work dengan akses token
# http://localhost:8000?dev_token=dev_local
```

### **2. Staging Environment:**
```bash
# Enable maintenance untuk staging
php artisan maintenance on \
  --message="Staging server maintenance" \
  --dev-token="staging_dev_2024" \
  --dev-ips="192.168.1.0/24"

# Team bisa akses dengan token atau dari office IP
```

### **3. Production Environment:**
```bash
# Enable maintenance untuk production
php artisan maintenance on \
  --message="Production server maintenance" \
  --dev-token="prod_emergency_2024" \
  --dev-ips="10.0.0.100,10.0.0.101" \
  --email="emergency@company.com"

# Hanya IP office yang bisa akses
```

## 🔒 Security Best Practices

### **1. Token Security:**
```bash
# Use strong, unique tokens
php artisan maintenance on --dev-token="dev_$(date +%s)_$(openssl rand -hex 8)"

# Rotate tokens regularly
# Old: dev_team_2024_jan
# New: dev_team_2024_feb
```

### **2. IP Security:**
```bash
# Use specific IPs, not ranges
php artisan maintenance on --dev-ips="192.168.1.100"  # ✅ Good
php artisan maintenance on --dev-ips="192.168.1.0/24" # ⚠️ Less secure

# Use VPN IPs for remote access
php artisan maintenance on --dev-ips="10.0.0.100,203.0.113.50"
```

### **3. Production Security:**
```bash
# Production: Use office IPs only
php artisan maintenance on --dev-ips="203.0.113.0/24"

# Production: Use strong tokens
php artisan maintenance on --dev-token="prod_$(openssl rand -hex 16)"
```

## 🚨 Emergency Access

### **Emergency Token:**
```bash
# Generate emergency token
php artisan maintenance on \
  --message="Emergency maintenance" \
  --dev-token="emergency_$(date +%s)" \
  --dev-ips="203.0.113.100" \
  --email="emergency@company.com"
```

### **Emergency IP Access:**
```bash
# Add emergency IP
php artisan maintenance on --dev-ips="203.0.113.100,203.0.113.101"

# Mobile hotspot IP (temporary)
php artisan maintenance on --dev-ips="192.168.43.100"
```

## 🔍 Troubleshooting

### **Common Issues:**

#### **1. Token Not Working:**
```bash
# Check if token is set correctly
php artisan maintenance status

# Verify token in URL
# Wrong: ?dev_token=dev_team_2024 (extra space)
# Correct: ?dev_token=dev_team_2024
```

#### **2. IP Not Whitelisted:**
```bash
# Check your current IP
curl ifconfig.me

# Check whitelisted IPs
php artisan maintenance status

# Add your IP
php artisan maintenance on --dev-ips="$(curl -s ifconfig.me)"
```

#### **3. Admin Login Not Working:**
```bash
# Check user role
php artisan tinker
>>> auth()->user()->hasRole(['admin', 'super_admin'])

# Check if user is logged in
>>> auth()->check()
```

#### **4. Config Not Updated:**
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

## 📱 Mobile Developer Access

### **Mobile Token Access:**
```
# Mobile browser
https://yourdomain.com?dev_token=dev_mobile_2024

# Mobile app (if applicable)
# Add token to API requests
```

### **Mobile IP Access:**
```bash
# Get mobile IP
# Use mobile hotspot, check IP, then whitelist
php artisan maintenance on --dev-ips="192.168.43.100"
```

## 🌐 Team Collaboration

### **Shared Development:**
```bash
# Team token untuk semua developer
php artisan maintenance on --dev-token="team_dev_2024"

# Office IP range untuk semua team
php artisan maintenance on --dev-ips="192.168.1.0/24"
```

### **Individual Access:**
```bash
# Personal token untuk developer
php artisan maintenance on --dev-token="dev_john_2024"

# Personal IP untuk developer
php artisan maintenance on --dev-ips="192.168.1.100"
```

## 📊 Monitoring Developer Access

### **Log Developer Access:**
```php
// Add to middleware
Log::info('Developer bypassed maintenance', [
    'method' => 'token', // or 'ip' or 'admin'
    'token' => $request->get('dev_token'),
    'ip' => $request->ip(),
    'user_id' => auth()->id(),
    'timestamp' => now()
]);
```

### **Track Access Attempts:**
```php
// Count developer access
Cache::increment('dev_maintenance_access');
Cache::put('last_dev_access', now());
```

## 🎯 Best Practices

### **1. Development:**
- ✅ **Use strong tokens** - Generate random, unique tokens
- ✅ **Limit IP access** - Only whitelist necessary IPs
- ✅ **Rotate credentials** - Change tokens regularly
- ✅ **Monitor access** - Log who accesses during maintenance

### **2. Production:**
- ✅ **Office IPs only** - Restrict to company network
- ✅ **Emergency tokens** - Have emergency access ready
- ✅ **Admin access** - Ensure admin accounts work
- ✅ **Documentation** - Keep access info documented

### **3. Team Management:**
- ✅ **Shared tokens** - Use team-wide tokens for collaboration
- ✅ **Individual access** - Personal tokens for individual work
- ✅ **Access control** - Monitor who has access
- ✅ **Regular cleanup** - Remove old tokens and IPs

## 📞 Developer Support

### **Emergency Contacts:**
- 🆘 **Emergency Dev**: emergency-dev@company.com | +62 812 9999 8888
- 💻 **Lead Developer**: lead-dev@company.com | +62 812 8888 7777
- 🔧 **DevOps**: devops@company.com | +62 812 7777 6666

### **Documentation:**
- 📖 **API Docs**: [Link to API documentation]
- 🔧 **Dev Environment**: [Link to dev setup guide]
- 🚀 **Deployment Guide**: [Link to deployment docs]

Developer access system sudah **lengkap dan aman** untuk semua skenario development dan production! 🚀👨‍💻
