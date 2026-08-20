# GAS-PAMAN — Versi Gabungan (Fix + Fitur Teman)

## Database
**Bisa pakai database yang sama** dengan versi fix terakhir.
- Enum `status_review` mendukung `draft` (runtime ALTER otomatis jika belum).
- Kolom `users.latitude` / `longitude` ditambah otomatis saat register/profil jika belum ada.
- Tidak ada migrasi wajib baru. Opsional: `database/migrations/20260812_draft_dan_agen_id.sql`

## Yang digabung

### Dari fix sebelumnya
1. ID Agen otomatis (format **AG-001**)
2. Hapus log laporan (centang / terpilih / semua)
3. Draft laporan penuh (tambah + edit + filter Draft di riwayat; default hanya yang terkirim)
4. Hapus hasil test agen (lengkap + hapus detail_jawaban)
5. Sidebar **fixed** (tidak ikut scroll)

### Dari editan teman
1. Register: alamat NTB (Kab/Kec/Desa) + peta pin + GPS
2. `ajax-geocode-public.php` (geocode tanpa login untuk register)
3. Halaman **Rangkuman Data** + menu sidebar (admin/kabalai)
4. Profil: ID Agen readonly; alamat utama diisi saat registrasi

## File penting
- register.php, profil.php, ajax-geocode-public.php, rangkuman-data.php
- tambah-catatan.php, edit-catatan.php, riwayat.php
- log-laporan.php, hasil-test-admin.php
- views/includes/sidebar.php, core/agen_id.php
