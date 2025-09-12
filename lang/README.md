# Bahasa Indonesia untuk Aplikasi EMS

Dokumentasi ini menjelaskan cara menggunakan sistem bahasa Indonesia yang telah diimplementasikan dalam aplikasi EMS.

## Struktur File Bahasa

```
lang/
├── id/                     # Bahasa Indonesia
│   ├── auth.php           # Pesan autentikasi
│   ├── common.php         # Teks umum UI
│   ├── ems.php            # Teks khusus aplikasi EMS
│   ├── email.php          # Template email
│   ├── messages.php       # Pesan sistem
│   ├── pagination.php     # Navigasi halaman
│   ├── passwords.php      # Reset password
│   └── validation.php     # Validasi form
├── en/                     # Bahasa Inggris (fallback)
│   ├── auth.php
│   ├── ems.php
│   ├── pagination.php
│   ├── passwords.php
│   └── validation.php
└── README.md              # Dokumentasi ini
```

## Konfigurasi

Aplikasi sudah dikonfigurasi untuk menggunakan bahasa Indonesia sebagai bahasa default:

```php
// config/app.php
'locale' => 'id',           // Bahasa default: Indonesia
'fallback_locale' => 'en',  // Bahasa fallback: Inggris
'faker_locale' => 'id_ID',  // Faker untuk testing
```

## Cara Menggunakan

### 1. Fungsi Translation di Blade

```blade
{{-- Menggunakan file ems.php --}}
{{ __('ems.dashboard') }}
{{ __('ems.employee') }}
{{ __('ems.save') }}

{{-- Menggunakan file common.php --}}
{{ __('common.welcome') }}
{{ __('common.hello') }}

{{-- Menggunakan file messages.php --}}
{{ __('messages.success.created') }}
{{ __('messages.error.not_found') }}

{{-- Menggunakan file email.php --}}
{{ __('email.subjects.welcome') }}
{{ __('email.templates.welcome.title') }}
```

### 2. Fungsi Translation di PHP

```php
// Di Controller atau Livewire Component
$message = __('ems.data_saved_successfully');
$title = __('ems.employee');

// Dengan parameter
$message = __('messages.success.created', ['name' => 'Employee']);
```

### 3. Menggunakan dalam Array

```php
// Di Livewire Component
public function mount()
{
    $this->breadcrumbs = [
        ['name' => __('ems.application'), 'url' => '/'],
        ['name' => __('ems.employee'), 'url' => route('employee.index')]
    ];
}
```

## File Bahasa yang Tersedia

### 1. `ems.php` - Teks Aplikasi EMS
Berisi semua teks yang berkaitan dengan fitur-fitur EMS:
- Navigasi dan menu
- Dashboard
- Manajemen karyawan
- Kehadiran
- Cuti dan lembur
- Laporan
- Dan lainnya

### 2. `common.php` - Teks Umum UI
Berisi teks-teks umum yang digunakan di seluruh aplikasi:
- Aksi umum (save, cancel, delete, dll)
- Status (active, inactive, pending, dll)
- Warna dan ukuran
- Arah dan waktu

### 3. `messages.php` - Pesan Sistem
Berisi pesan-pesan sistem yang dikelompokkan berdasarkan jenis:
- `success` - Pesan sukses
- `error` - Pesan error
- `warning` - Pesan peringatan
- `info` - Pesan informasi
- `confirm` - Pesan konfirmasi

### 4. `email.php` - Template Email
Berisi template dan subjek email:
- Subjek email
- Template email
- Greeting dan closing
- Footer email

### 5. `validation.php` - Validasi Form
Berisi pesan validasi form dengan atribut yang sudah diterjemahkan.

## Menambah Teks Baru

### 1. Menambah ke File yang Sudah Ada

```php
// lang/id/ems.php
return [
    // ... existing translations
    'new_feature' => 'Fitur Baru',
    'new_button' => 'Tombol Baru',
];
```

### 2. Membuat File Baru

```php
// lang/id/custom.php
return [
    'custom_message' => 'Pesan Kustom',
    'custom_action' => 'Aksi Kustom',
];

// Penggunaan
{{ __('custom.custom_message') }}
```

## Best Practices

### 1. Penamaan Key
- Gunakan snake_case untuk key
- Gunakan nama yang deskriptif
- Kelompokkan berdasarkan fitur

```php
// Baik
'employee_name' => 'Nama Karyawan',
'leave_request' => 'Permintaan Cuti',

// Hindari
'emp_name' => 'Nama Karyawan',
'leave_req' => 'Permintaan Cuti',
```

### 2. Struktur File
- Pisahkan berdasarkan konteks penggunaan
- Gunakan array nested untuk mengelompokkan

```php
// messages.php
'success' => [
    'created' => 'Data berhasil dibuat',
    'updated' => 'Data berhasil diperbarui',
    'deleted' => 'Data berhasil dihapus',
],
```

### 3. Konsistensi
- Gunakan terminologi yang konsisten
- Sesuaikan dengan konteks bisnis
- Pertahankan tone yang sama

## Testing

Untuk menguji bahasa Indonesia:

1. Pastikan `APP_LOCALE=id` di file `.env`
2. Clear cache: `php artisan config:clear`
3. Akses aplikasi dan periksa teks yang ditampilkan

## Mengubah Bahasa

Untuk mengubah bahasa secara dinamis:

```php
// Di Controller atau Middleware
app()->setLocale('en'); // Ganti ke Inggris
app()->setLocale('id'); // Ganti ke Indonesia
```

## Troubleshooting

### 1. Teks Tidak Muncul
- Pastikan key ada di file bahasa
- Periksa syntax PHP
- Clear cache: `php artisan config:clear`

### 2. Fallback ke Bahasa Inggris
- Pastikan file bahasa Inggris ada
- Periksa konfigurasi `fallback_locale`

### 3. Cache Masalah
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Kontribusi

Untuk menambah atau memperbaiki terjemahan:

1. Edit file yang sesuai di folder `lang/id/`
2. Pastikan konsistensi dengan file yang ada
3. Test perubahan di aplikasi
4. Update dokumentasi jika diperlukan

## Catatan Penting

- Selalu gunakan fungsi `__()` untuk teks yang akan ditampilkan ke user
- Jangan hardcode teks dalam bahasa tertentu
- Pastikan semua teks user-facing menggunakan sistem translation
- Test dengan bahasa yang berbeda untuk memastikan UI tidak rusak
