-- Migrasi: simpan set 15 soal acak per percobaan test (pre/post) agar
-- refresh tidak mengganti soal, dan pembahasan sesuai yang dikerjakan agen.
CREATE TABLE IF NOT EXISTS hasil_test_soal (
    id INT NOT NULL AUTO_INCREMENT,
    hasil_test_id INT NOT NULL,
    pertanyaan_id INT NOT NULL,
    urutan INT NOT NULL,
    PRIMARY KEY (id),
    KEY idx_hts_hasil (hasil_test_id),
    KEY idx_hts_pertanyaan (pertanyaan_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
