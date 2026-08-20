ALTER TABLE catatan_harian
    ADD COLUMN usia INT NULL AFTER nama_konsumen,
    ADD COLUMN jenis_kelamin ENUM('Pria', 'Wanita') NULL AFTER usia,
    ADD COLUMN pekerjaan VARCHAR(100) NULL AFTER jenis_kelamin,
    ADD COLUMN hasil_pre_test VARCHAR(255) NULL AFTER no_hp,
    ADD COLUMN hasil_post_test VARCHAR(255) NULL AFTER hasil_pre_test;
