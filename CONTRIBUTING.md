# Panduan Kontribusi

Terima kasih atas minat Anda untuk berkontribusi pada proyek SIARS. Dokumen ini berisi panduan untuk berkontribusi pada proyek ini.

## Alur Kerja Git

1. Fork repositori ini
2. Buat branch baru untuk fitur yang akan dikembangkan: `git checkout -b fitur-baru`
3. Commit perubahan Anda: `git commit -m 'Menambahkan fitur baru'`
4. Push ke branch: `git push origin fitur-baru`
5. Buat Pull Request

## Standar Kode

- Ikuti PSR-12 untuk standar penulisan kode PHP
- Gunakan fitur terbaru PHP 8.1+
- Tulis komentar yang jelas untuk metode dan kelas yang kompleks
- Tambahkan docblocks untuk semua metode dan kelas
- Pastikan kode lulus semua tes sebelum submit

## Pengembangan Fitur Baru

1. Buat issue terlebih dahulu untuk mendiskusikan fitur yang ingin dikembangkan
2. Dapatkan persetujuan dari maintainer sebelum mulai mengerjakan fitur besar
3. Buat branch baru untuk setiap fitur
4. Pastikan ada tes untuk setiap fitur baru

## Pelaporan Bug

Jika Anda menemukan bug, silakan buat issue dengan informasi berikut:
- Ringkasan masalah
- Langkah-langkah untuk mereproduksi bug
- Hasil yang diharapkan vs hasil yang diperoleh
- Screenshot (jika memungkinkan)
- Informasi lingkungan (OS, browser, versi PHP, dll.)

## Struktur Branch

- `main` - Branch produksi yang stabil, selalu siap digunakan
- `develop` - Branch pengembangan untuk integrasi fitur yang telah selesai
- `feature/[nama-fitur]` - Branch untuk pengembangan fitur baru
- `bugfix/[nama-bug]` - Branch untuk perbaikan bug
- `release/[versi]` - Branch persiapan rilis

## Rilis

1. Versi mayor (1.0.0): Perubahan yang memecah kompatibilitas
2. Versi minor (0.1.0): Penambahan fitur dengan kompatibilitas mundur
3. Versi patch (0.0.1): Perbaikan bug dengan kompatibilitas mundur

## Konvensi Pesan Commit

Format: `[type]: [subject]`

Types:
- `feat`: Fitur baru
- `fix`: Perbaikan bug
- `docs`: Perubahan dokumentasi
- `style`: Perubahan yang tidak memengaruhi kode (pemformatan, spasi)
- `refactor`: Refaktor kode
- `test`: Menambah atau memperbaiki tes
- `chore`: Perubahan proses build atau alat bantu 