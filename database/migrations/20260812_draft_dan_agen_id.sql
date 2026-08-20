-- Migrasi: status draft untuk catatan + pastikan agen_id auto sequential
-- Jalankan sekali. Abaikan error jika sudah ada.

-- 1. Tambah status 'draft' ke enum status_review
ALTER TABLE catatan_harian
  MODIFY COLUMN status_review ENUM('draft','pending','approved','revisi') NOT NULL DEFAULT 'pending';

-- 2. Index untuk filter draft
ALTER TABLE catatan_harian ADD INDEX idx_ch_status_draft (status_review);

