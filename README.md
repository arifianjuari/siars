# Sistem Informasi Akreditasi Rumah Sakit (SIARS)

## Tentang SIARS

Sistem Informasi Akreditasi Rumah Sakit (SIARS) adalah aplikasi berbasis web yang dirancang untuk membantu rumah sakit dalam mengelola dan memantau proses akreditasi berdasarkan standar SNARS (Standar Nasional Akreditasi Rumah Sakit). Sistem ini memudahkan rumah sakit untuk mendokumentasikan, melacak, dan melaporkan kepatuhan terhadap standar akreditasi yang ditetapkan.

## Fitur Utama

- **Manajemen Dokumen**: Pengelolaan dokumen kebijakan, prosedur, dan bukti pendukung akreditasi
- **Monitoring Standar**: Pemantauan kepatuhan terhadap standar SNARS
- **Penilaian Mandiri**: Evaluasi internal terhadap kesiapan akreditasi
- **Manajemen Tindak Lanjut**: Pengelolaan temuan dan rencana perbaikan
- **Dashboard Eksekutif**: Visualisasi status akreditasi secara real-time
- **Manajemen Pengguna**: Pengaturan hak akses berdasarkan peran dan tanggung jawab

## Teknologi

- **Framework**: Laravel
- **Database**: MySQL
- **Frontend**: Bootstrap, jQuery, dan Vue.js

## Persyaratan Sistem

- PHP >= 8.1
- MySQL >= 5.7
- Composer
- Node.js dan NPM

## Instalasi

1. Clone repositori ini
2. Jalankan `composer install`
3. Salin file `.env.example` ke `.env` dan sesuaikan konfigurasi database
4. Jalankan `php artisan key:generate`
5. Jalankan `php artisan migrate`
6. Jalankan `php artisan db:seed` untuk mengisi data awal
7. Jalankan `npm install && npm run dev`
8. Jalankan `php artisan serve` untuk memulai server pengembangan

## Kontribusi

Silakan berkontribusi dengan membuat pull request atau melaporkan masalah melalui issue tracker.

## Lisensi

SIARS adalah perangkat lunak berpemilik yang dikembangkan untuk penggunaan internal rumah sakit.
