# Perubahan GAS-PAMAN — 12 Agustus 2026

## 1. ID Agen otomatis berdasarkan created_at

- Saat registrasi, `agen_id` digenerate otomatis (format `001`, `002`, …).
- User yang lebih dulu membuat akun mendapat nomor lebih kecil.
- Helper: `core/agen_id.php` (`generate_next_agen_id`, `backfill_agen_ids`, `assign_agen_id_on_register`).
- Backfill otomatis di `daftar-agen.php` untuk agen lama yang belum punya ID.
- Di profil agen, field ID Agen bersifat **readonly** (tidak diisi manual).

**File terkait:** `register.php`, `profil.php`, `daftar-agen.php`, `core/agen_id.php`

---

## 2. Hapus log di fitur Log Laporan (admin)

- Tombol **Hapus** di samping **Lihat** pada setiap baris log.
- Checkbox per baris + **Pilih Semua**.
- Tombol **Hapus Terpilih** (multi-delete).
- Tombol **Hapus Semua (Filter Saat Ini)** — menghapus semua log sesuai filter aktif (atau seluruh log jika tanpa filter).
- Konfirmasi SweetAlert2 sebelum hapus.

**File terkait:** `log-laporan.php`

---

## 3. Draft laporan (role agen)

### Perilaku
- Di form **Tambah Catatan** / **Edit Catatan** ada 2 tombol:
  - **Simpan sebagai Draft** → `status_review = 'draft'` (belum terkirim ke admin).
  - **Kirim Laporan** → `status_review = 'pending'` (menunggu review).
- Draft **tidak** masuk log aktivitas sebagai “buat laporan”.
- Bukti foto/video **wajib** hanya saat kirim; saat draft boleh kosong.

### Riwayat Laporan
- Default hanya menampilkan laporan yang **sudah terkirim** (pending / approved / revisi).
- Tombol **Draft** di sebelah Filter & Reset → menampilkan hanya draft agen.
- Dari daftar draft, agen bisa **Edit** → kembali ke form edit, lengkapi data, lalu kirim atau simpan draft lagi.
- Badge status “draft” berwarna biru.

### Database
- Enum `status_review` diperluas: `'draft','pending','approved','revisi'`.
- Migrasi: `database/migrations/20260812_draft_dan_agen_id.sql`
- Runtime `ALTER TABLE` idempotent di `tambah-catatan.php`, `edit-catatan.php`, `riwayat.php`.

**File terkait:** `tambah-catatan.php`, `edit-catatan.php`, `riwayat.php`, `detail-catatan.php`, `dashboard.php`

---

## Cara deploy
1. Upload semua file yang berubah (overwrite).
2. (Opsional) Jalankan migrasi SQL:
   ```sql
   source database/migrations/20260812_draft_dan_agen_id.sql
   ```
   Atau biarkan runtime ALTER di halaman agen menyesuaikan enum.
3. Buka **Daftar Agen** sekali sebagai admin agar backfill `agen_id` berjalan untuk user lama.

---

## 4. Hapus hasil test agen (admin/staff)

Di halaman **Hasil Test Agen** (`hasil-test-admin.php`), sama seperti log laporan:

- Tombol **Hapus** (ikon trash) di kolom Aksi, bersebelahan dengan Detail / Edit
- Checkbox per baris + **Pilih Semua**
- **Hapus Terpilih** (multi-delete)
- **Hapus Semua (Filter Saat Ini)** — sesuai filter jenis/agen yang aktif
- Konfirmasi SweetAlert2
- Saat hapus: `detail_jawaban` ikut dihapus, lalu baris `hasil_test`
- Hanya role **admin** dan **staff** yang boleh hapus

