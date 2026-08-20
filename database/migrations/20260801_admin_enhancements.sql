-- Migrasi peningkatan Admin GAS-PAMAN
-- Jalankan sekali. Jika kolom sudah ada, lewati baris yang error.

-- 1. Field tambahan profil agen
ALTER TABLE users ADD COLUMN pekerjaan VARCHAR(100) NULL AFTER nomor_hp;
ALTER TABLE users ADD COLUMN kampus VARCHAR(150) NULL AFTER pekerjaan;
ALTER TABLE users ADD COLUMN jurusan VARCHAR(150) NULL AFTER kampus;
ALTER TABLE users ADD COLUMN tanda_tangan VARCHAR(255) NULL AFTER foto_profil;

-- Role kabalai
ALTER TABLE users MODIFY role ENUM('admin','staff','agen','kabalai') NOT NULL DEFAULT 'agen';
ALTER TABLE users MODIFY nama_instansi VARCHAR(150) NOT NULL;

-- 2. Status sertifikat + input manual nilai
ALTER TABLE hasil_test ADD COLUMN status_sertifikat ENUM('belum','menunggu_ttd','disetujui') DEFAULT 'belum' AFTER created_at;
ALTER TABLE hasil_test ADD COLUMN signed_by INT NULL AFTER status_sertifikat;
ALTER TABLE hasil_test ADD COLUMN signed_at DATETIME NULL AFTER signed_by;
ALTER TABLE hasil_test ADD COLUMN is_manual TINYINT(1) DEFAULT 0 AFTER signed_at;
ALTER TABLE hasil_test ADD COLUMN catatan_manual TEXT NULL AFTER is_manual;

-- 3. Kategori soal (5 bagian)
ALTER TABLE pertanyaan ADD COLUMN kategori ENUM(
    'umum',
    'komoditi_pangan',
    'kosmetik',
    'obat_bahan_alam',
    'obat'
) DEFAULT 'umum' AFTER teks_pertanyaan;

-- 4. Index
ALTER TABLE catatan_harian ADD INDEX idx_ch_tanggal (tanggal);
ALTER TABLE catatan_harian ADD INDEX idx_ch_status (status_review);
ALTER TABLE hasil_test ADD INDEX idx_ht_status_sert (status_sertifikat);