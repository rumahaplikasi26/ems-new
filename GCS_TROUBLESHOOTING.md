# Google Cloud Storage (GCS) Troubleshooting Guide

## Masalah: File tidak tersimpan di GCS meskipun tidak ada error

### Penyebab Umum:

1. **Konfigurasi Environment Variables Tidak Lengkap**
2. **Service Account Permissions Tidak Cukup**
3. **Bucket Configuration Issues**
4. **Network/Firewall Issues**

## Solusi yang Telah Diimplementasikan:

### 1. Enhanced Error Handling & Logging
- Menambahkan logging detail untuk setiap step upload
- Error handling yang lebih baik dengan fallback ke local storage
- Validasi koneksi GCS sebelum upload

### 2. Fallback Mechanism
- Jika GCS tidak tersedia, sistem akan otomatis menggunakan local storage
- Tidak akan mengganggu functionality aplikasi

### 3. Test Command
Jalankan command berikut untuk test koneksi GCS:
```bash
php artisan gcs:test
```

## Environment Variables yang Diperlukan:

```env
# Google Cloud Storage Configuration
GOOGLE_CLOUD_PROJECT_ID=your-project-id
GOOGLE_CLOUD_STORAGE_BUCKET=your-bucket-name
GOOGLE_CLOUD_CLIENT_EMAIL=your-service-account@your-project-id.iam.gserviceaccount.com
GOOGLE_CLOUD_PRIVATE_KEY_ID=your-private-key-id
GOOGLE_CLOUD_PRIVATE_KEY="-----BEGIN PRIVATE KEY-----\nYour-Private-Key-Here\n-----END PRIVATE KEY-----\n"
GOOGLE_CLOUD_CLIENT_ID=your-client-id
GOOGLE_CLOUD_CLIENT_X509_CERT_URL=https://www.googleapis.com/robot/v1/metadata/x509/your-service-account%40your-project-id.iam.gserviceaccount.com
```

## Checklist Troubleshooting:

### 1. Cek Environment Variables
```bash
php artisan config:show filesystems.disks.gcs
```

### 2. Cek Service Account Permissions
Service account harus memiliki permissions:
- `Storage Object Admin` atau
- `Storage Object Creator` + `Storage Object Viewer`

### 3. Cek Bucket Configuration
- Bucket harus ada dan accessible
- Bucket harus memiliki public access jika menggunakan public URLs
- Cek bucket region dan endpoint

### 4. Cek Network Connectivity
```bash
# Test koneksi ke Google Cloud
curl -I https://storage.googleapis.com/your-bucket-name
```

### 5. Cek Logs
```bash
tail -f storage/logs/laravel.log
```

## Perubahan yang Dibuat:

### 1. ProfileForm.php
- Enhanced error handling dengan try-catch
- Detailed logging untuk debugging
- Fallback mechanism ke local storage
- Method `getStorageDisk()` untuk test koneksi
- Method `updatedAvatar()` untuk preview yang lebih baik

### 2. TestGCSConnection.php
- Command untuk test koneksi GCS
- Comprehensive testing (create, read, delete, URL generation)
- Environment variables validation

### 3. filesystems.php
- Menambahkan `'throw' => false` untuk GCS disk
- Better error handling configuration

## Cara Menggunakan:

1. **Upload Avatar Normal:**
   - Sistem akan mencoba upload ke GCS
   - Jika berhasil, file akan tersimpan di GCS
   - Jika gagal, akan fallback ke local storage

2. **Debugging:**
   - Jalankan `php artisan gcs:test` untuk test koneksi
   - Cek logs di `storage/logs/laravel.log`
   - Monitor error messages di aplikasi

3. **Manual Test:**
   ```php
   // Test di tinker
   php artisan tinker
   Storage::disk('gcs')->put('test.txt', 'Hello World');
   ```

## Tips:

1. **Pastikan Service Account Key Format Benar:**
   - Private key harus dalam format yang benar dengan `\n` untuk newlines
   - Gunakan double quotes untuk private key di .env

2. **Bucket Permissions:**
   - Pastikan bucket memiliki public read access jika menggunakan public URLs
   - Atau gunakan signed URLs untuk private access

3. **Network Issues:**
   - Cek firewall settings
   - Pastikan server bisa akses internet
   - Cek proxy settings jika ada

4. **Monitoring:**
   - Monitor GCS usage di Google Cloud Console
   - Set up billing alerts
   - Monitor storage costs
