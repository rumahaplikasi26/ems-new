<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Email Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used for email templates and
    | email-related messages.
    |
    */

    // Email Subjects
    'subjects' => [
        'welcome' => 'Selamat Datang di EMS',
        'password_reset' => 'Reset Kata Sandi',
        'email_verification' => 'Verifikasi Email',
        'account_activation' => 'Aktivasi Akun',
        'account_deactivation' => 'Deaktivasi Akun',
        'password_changed' => 'Kata Sandi Diubah',
        'profile_updated' => 'Profil Diperbarui',
        'login_notification' => 'Notifikasi Login',
        'security_alert' => 'Alert Keamanan',
        'system_notification' => 'Notifikasi Sistem',
        'maintenance_notice' => 'Pemberitahuan Maintenance',
        'backup_completed' => 'Backup Selesai',
        'backup_failed' => 'Backup Gagal',
        'data_export' => 'Ekspor Data',
        'data_import' => 'Impor Data',
        'leave_request' => 'Permintaan Cuti',
        'leave_approved' => 'Cuti Disetujui',
        'leave_rejected' => 'Cuti Ditolak',
        'overtime_request' => 'Permintaan Lembur',
        'overtime_approved' => 'Lembur Disetujui',
        'overtime_rejected' => 'Lembur Ditolak',
        'attendance_reminder' => 'Pengingat Kehadiran',
        'attendance_report' => 'Laporan Kehadiran',
        'financial_request' => 'Permintaan Keuangan',
        'financial_approved' => 'Keuangan Disetujui',
        'financial_rejected' => 'Keuangan Ditolak',
        'daily_report' => 'Laporan Harian',
        'visit_report' => 'Laporan Kunjungan',
        'announcement' => 'Pengumuman',
        'meeting_reminder' => 'Pengingat Meeting',
        'deadline_reminder' => 'Pengingat Deadline',
        'task_assigned' => 'Tugas Ditetapkan',
        'task_completed' => 'Tugas Selesai',
        'task_overdue' => 'Tugas Terlambat',
    ],

    // Email Greetings
    'greetings' => [
        'hello' => 'Halo',
        'dear' => 'Yang Terhormat',
        'hi' => 'Hai',
        'good_morning' => 'Selamat Pagi',
        'good_afternoon' => 'Selamat Siang',
        'good_evening' => 'Selamat Sore',
    ],

    // Email Closings
    'closings' => [
        'best_regards' => 'Salam Hormat',
        'sincerely' => 'Hormat Kami',
        'thank_you' => 'Terima Kasih',
        'regards' => 'Salam',
        'yours_truly' => 'Hormat Kami',
        'best' => 'Terbaik',
    ],

    // Email Templates
    'templates' => [
        'welcome' => [
            'title' => 'Selamat Datang di EMS',
            'content' => 'Selamat datang di sistem EMS. Akun Anda telah berhasil dibuat dan siap digunakan.',
            'action' => 'Masuk ke Sistem',
        ],
        'password_reset' => [
            'title' => 'Reset Kata Sandi',
            'content' => 'Anda telah meminta reset kata sandi. Klik tombol di bawah untuk mereset kata sandi Anda.',
            'action' => 'Reset Kata Sandi',
            'expiry' => 'Link ini akan berakhir dalam :minutes menit.',
        ],
        'email_verification' => [
            'title' => 'Verifikasi Email',
            'content' => 'Silakan verifikasi alamat email Anda dengan mengklik tombol di bawah.',
            'action' => 'Verifikasi Email',
        ],
        'account_activation' => [
            'title' => 'Aktivasi Akun',
            'content' => 'Akun Anda telah diaktifkan dan siap digunakan.',
            'action' => 'Masuk ke Sistem',
        ],
        'account_deactivation' => [
            'title' => 'Deaktivasi Akun',
            'content' => 'Akun Anda telah dinonaktifkan. Hubungi administrator untuk informasi lebih lanjut.',
        ],
        'password_changed' => [
            'title' => 'Kata Sandi Diubah',
            'content' => 'Kata sandi Anda telah berhasil diubah. Jika Anda tidak melakukan perubahan ini, segera hubungi administrator.',
        ],
        'profile_updated' => [
            'title' => 'Profil Diperbarui',
            'content' => 'Profil Anda telah berhasil diperbarui.',
        ],
        'login_notification' => [
            'title' => 'Notifikasi Login',
            'content' => 'Ada aktivitas login baru pada akun Anda.',
            'details' => 'Waktu: :time, IP: :ip, Browser: :browser',
        ],
        'security_alert' => [
            'title' => 'Alert Keamanan',
            'content' => 'Terjadi aktivitas mencurigakan pada akun Anda.',
            'action' => 'Periksa Akun',
        ],
        'leave_request' => [
            'title' => 'Permintaan Cuti',
            'content' => 'Ada permintaan cuti baru yang memerlukan persetujuan Anda.',
            'details' => 'Karyawan: :employee, Tipe: :type, Tanggal: :dates, Durasi: :duration hari',
            'action' => 'Tinjau Permintaan',
        ],
        'leave_approved' => [
            'title' => 'Cuti Disetujui',
            'content' => 'Permintaan cuti Anda telah disetujui.',
            'details' => 'Tipe: :type, Tanggal: :dates, Durasi: :duration hari',
        ],
        'leave_rejected' => [
            'title' => 'Cuti Ditolak',
            'content' => 'Permintaan cuti Anda telah ditolak.',
            'reason' => 'Alasan: :reason',
        ],
        'overtime_request' => [
            'title' => 'Permintaan Lembur',
            'content' => 'Ada permintaan lembur baru yang memerlukan persetujuan Anda.',
            'details' => 'Karyawan: :employee, Tanggal: :date, Durasi: :duration jam',
            'action' => 'Tinjau Permintaan',
        ],
        'overtime_approved' => [
            'title' => 'Lembur Disetujui',
            'content' => 'Permintaan lembur Anda telah disetujui.',
            'details' => 'Tanggal: :date, Durasi: :duration jam',
        ],
        'overtime_rejected' => [
            'title' => 'Lembur Ditolak',
            'content' => 'Permintaan lembur Anda telah ditolak.',
            'reason' => 'Alasan: :reason',
        ],
        'attendance_reminder' => [
            'title' => 'Pengingat Kehadiran',
            'content' => 'Jangan lupa untuk melakukan check-in/check-out hari ini.',
            'action' => 'Check In/Out',
        ],
        'attendance_report' => [
            'title' => 'Laporan Kehadiran',
            'content' => 'Laporan kehadiran Anda untuk periode :period.',
            'action' => 'Lihat Laporan',
        ],
        'financial_request' => [
            'title' => 'Permintaan Keuangan',
            'content' => 'Ada permintaan keuangan baru yang memerlukan persetujuan Anda.',
            'details' => 'Karyawan: :employee, Jumlah: :amount, Tujuan: :purpose',
            'action' => 'Tinjau Permintaan',
        ],
        'financial_approved' => [
            'title' => 'Keuangan Disetujui',
            'content' => 'Permintaan keuangan Anda telah disetujui.',
            'details' => 'Jumlah: :amount, Tujuan: :purpose',
        ],
        'financial_rejected' => [
            'title' => 'Keuangan Ditolak',
            'content' => 'Permintaan keuangan Anda telah ditolak.',
            'reason' => 'Alasan: :reason',
        ],
        'daily_report' => [
            'title' => 'Laporan Harian',
            'content' => 'Laporan harian Anda untuk tanggal :date.',
            'action' => 'Lihat Laporan',
        ],
        'visit_report' => [
            'title' => 'Laporan Kunjungan',
            'content' => 'Laporan kunjungan Anda untuk tanggal :date.',
            'action' => 'Lihat Laporan',
        ],
        'announcement' => [
            'title' => 'Pengumuman',
            'content' => 'Ada pengumuman baru yang perlu Anda baca.',
            'action' => 'Baca Pengumuman',
        ],
        'meeting_reminder' => [
            'title' => 'Pengingat Meeting',
            'content' => 'Meeting akan dimulai dalam :minutes menit.',
            'details' => 'Judul: :title, Waktu: :time, Lokasi: :location',
        ],
        'deadline_reminder' => [
            'title' => 'Pengingat Deadline',
            'content' => 'Deadline tugas Anda akan segera berakhir.',
            'details' => 'Tugas: :task, Deadline: :deadline',
        ],
        'task_assigned' => [
            'title' => 'Tugas Ditetapkan',
            'content' => 'Anda telah ditetapkan tugas baru.',
            'details' => 'Tugas: :task, Deadline: :deadline',
            'action' => 'Lihat Tugas',
        ],
        'task_completed' => [
            'title' => 'Tugas Selesai',
            'content' => 'Tugas telah diselesaikan.',
            'details' => 'Tugas: :task, Diselesaikan oleh: :user',
        ],
        'task_overdue' => [
            'title' => 'Tugas Terlambat',
            'content' => 'Tugas Anda telah melewati deadline.',
            'details' => 'Tugas: :task, Deadline: :deadline',
            'action' => 'Lihat Tugas',
        ],
    ],

    // Email Footer
    'footer' => [
        'company_name' => 'EMS System',
        'company_address' => 'Alamat Perusahaan',
        'company_phone' => 'Telepon Perusahaan',
        'company_email' => 'Email Perusahaan',
        'website' => 'Website Perusahaan',
        'unsubscribe' => 'Berhenti Berlangganan',
        'privacy_policy' => 'Kebijakan Privasi',
        'terms_of_service' => 'Syarat Layanan',
        'contact_support' => 'Hubungi Dukungan',
        'powered_by' => 'Didukung oleh',
    ],

    // Email Common Text
    'common' => [
        'if_you_did_not_request' => 'Jika Anda tidak meminta tindakan ini, abaikan email ini.',
        'do_not_reply' => 'Jangan membalas email ini.',
        'contact_support' => 'Jika Anda mengalami masalah, hubungi dukungan teknis.',
        'thank_you_for_using' => 'Terima kasih telah menggunakan sistem kami.',
        'this_is_automated' => 'Ini adalah email otomatis, jangan membalas.',
        'keep_secure' => 'Jaga keamanan akun Anda dengan tidak membagikan kredensial login.',
        'report_suspicious' => 'Laporkan aktivitas mencurigakan segera.',
        'update_profile' => 'Pastikan profil Anda selalu up-to-date.',
        'backup_data' => 'Selalu backup data penting Anda.',
        'stay_updated' => 'Tetap update dengan pengumuman terbaru.',
    ],

];
