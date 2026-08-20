ALTER TABLE catatan_harian
    ADD COLUMN nilai_pre_test DECIMAL(5,2) NULL AFTER hasil_post_test,
    ADD COLUMN nilai_post_test DECIMAL(5,2) NULL AFTER nilai_pre_test,
    ADD COLUMN lampiran_hasil_test VARCHAR(255) NULL AFTER nilai_post_test;
