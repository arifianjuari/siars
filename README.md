# SIARS - Sistem Informasi Akreditasi Rumah Sakit

Aplikasi manajemen akreditasi rumah sakit yang memudahkan proses akreditasi SNARS dan manajemen risiko.

## Fitur Utama

- Manajemen Akreditasi SNARS
- Manajemen Risiko
- Multi-tenant untuk beberapa rumah sakit
- Multi-role (Superadmin, Tenant Admin, Manajemen Strategis, Manajemen Eksekutif, Manajemen Operasional, Staf)
- Aktivasi dan manajemen modul

## Teknologi

- Laravel
- MySQL
- Blade Templates
- Spatie Permission

## Instalasi

### Persyaratan

- PHP >= 8.1
- Composer
- MySQL atau database yang didukung Laravel

### Langkah Instalasi

1. Clone repositori ini:
   ```
   git clone [url-repository]
   ```

2. Pindah ke direktori proyek:
   ```
   cd siars
   ```

3. Instal dependensi dengan Composer:
   ```
   composer install
   ```

4. Salin file .env.example menjadi .env:
   ```
   cp .env.example .env
   ```

5. Generate application key:
   ```
   php artisan key:generate
   ```

6. Konfigurasi database di file .env

7. Jalankan migrasi dan seeder:
   ```
   php artisan migrate --seed
   ```

8. Jalankan server pengembangan:
   ```
   php artisan serve
   ```

## Kredensial Default

### Superadmin
- Email: superadmin@example.com
- Password: password

## Lisensi

[MIT License](LICENSE)
