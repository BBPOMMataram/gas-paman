<?php
/**
 * Helper log aktivitas laporan agen + sampling konsumen.
 */

function log_laporan_ensure_table(PDO $pdo): void {
    static $checked = false;
    if ($checked) return;
    $checked = true;
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS log_laporan (
                id INT NOT NULL AUTO_INCREMENT,
                catatan_id INT NULL,
                user_id INT NOT NULL,
                aksi VARCHAR(20) NOT NULL,
                keterangan TEXT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_log_user (user_id),
                KEY idx_log_catatan (catatan_id),
                KEY idx_log_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    } catch (Exception $e) {
        error_log('log_laporan_ensure_table: ' . $e->getMessage());
    }
}

function sampling_ensure_tables(PDO $pdo): void {
    static $checked = false;
    if ($checked) return;
    $checked = true;
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS sampling_periode (
                id INT NOT NULL AUTO_INCREMENT,
                dibuat_oleh INT NOT NULL,
                jumlah INT NOT NULL DEFAULT 20,
                status ENUM('aktif','selesai') NOT NULL DEFAULT 'aktif',
                filter_info VARCHAR(500) NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                reset_at TIMESTAMP NULL,
                PRIMARY KEY (id),
                KEY idx_sampling_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS sampling_hasil (
                id INT NOT NULL AUTO_INCREMENT,
                periode_id INT NOT NULL,
                catatan_id INT NOT NULL,
                urutan INT NOT NULL,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uq_periode_catatan (periode_id, catatan_id),
                KEY idx_sampling_periode (periode_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    } catch (Exception $e) {
        error_log('sampling_ensure_tables: ' . $e->getMessage());
    }
}

/**
 * Catat aktivitas laporan. Return true jika berhasil.
 */
function log_laporan(PDO $pdo, $userId, $aksi, $catatanId = null, $keterangan = null): bool {
    $allowed = ['buat', 'edit', 'approve', 'revisi', 'hapus'];
    $aksi = strtolower(trim((string)$aksi));
    if (!in_array($aksi, $allowed, true)) {
        error_log('log_laporan: aksi tidak valid: ' . $aksi);
        return false;
    }
    $userId = (int)$userId;
    $catatanId = ($catatanId === null || $catatanId === '') ? null : (int)$catatanId;
    if ($userId <= 0) {
        error_log('log_laporan: user_id tidak valid');
        return false;
    }

    try {
        log_laporan_ensure_table($pdo);
        $stmt = $pdo->prepare("INSERT INTO log_laporan (catatan_id, user_id, aksi, keterangan) VALUES (?, ?, ?, ?)");
        $ok = $stmt->execute([$catatanId, $userId, $aksi, $keterangan !== null ? (string)$keterangan : null]);
        if (!$ok) {
            error_log('log_laporan: execute false');
            return false;
        }
        return true;
    } catch (Throwable $e) {
        // Fallback: mungkin kolom aksi masih ENUM ketat / struktur beda
        error_log('log_laporan insert gagal: ' . $e->getMessage());
        try {
            $stmt2 = $pdo->prepare("INSERT INTO log_laporan (catatan_id, user_id, aksi, keterangan, created_at) VALUES (?, ?, ?, ?, NOW())");
            return $stmt2->execute([$catatanId, $userId, $aksi, $keterangan !== null ? (string)$keterangan : null]);
        } catch (Throwable $e2) {
            error_log('log_laporan fallback gagal: ' . $e2->getMessage());
            return false;
        }
    }
}

function sampling_get_aktif(PDO $pdo): ?array {
    sampling_ensure_tables($pdo);
    $stmt = $pdo->query("SELECT * FROM sampling_periode WHERE status = 'aktif' ORDER BY id DESC LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function sampling_get_hasil_aktif(PDO $pdo): array {
    $periode = sampling_get_aktif($pdo);
    if (!$periode) return [];

    $stmt = $pdo->prepare("
        SELECT sh.urutan, sh.catatan_id, c.*, u.nama as nama_agen
        FROM sampling_hasil sh
        JOIN catatan_harian c ON c.id = sh.catatan_id
        JOIN users u ON u.id = c.user_id
        WHERE sh.periode_id = ?
        ORDER BY sh.urutan ASC
    ");
    $stmt->execute([(int)$periode['id']]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function sampling_simpan(PDO $pdo, int $adminId, array $catatanIds, ?string $filterInfo = null): array {
    sampling_ensure_tables($pdo);

    $aktif = sampling_get_aktif($pdo);
    if ($aktif) {
        return ['ok' => false, 'msg' => 'Masih ada hasil sampling aktif. Klik Reset dulu sebelum mengacak periode baru.'];
    }
    if (empty($catatanIds)) {
        return ['ok' => false, 'msg' => 'Tidak ada data konsumen untuk diundi.'];
    }

    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO sampling_periode (dibuat_oleh, jumlah, status, filter_info) VALUES (?, ?, 'aktif', ?)");
        $stmt->execute([$adminId, count($catatanIds), $filterInfo]);
        $periodeId = (int)$pdo->lastInsertId();

        $ins = $pdo->prepare("INSERT INTO sampling_hasil (periode_id, catatan_id, urutan) VALUES (?, ?, ?)");
        $urutan = 1;
        foreach ($catatanIds as $cid) {
            $ins->execute([$periodeId, (int)$cid, $urutan++]);
        }
        $pdo->commit();
        return ['ok' => true, 'periode_id' => $periodeId, 'jumlah' => count($catatanIds)];
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log('sampling_simpan: ' . $e->getMessage());
        return ['ok' => false, 'msg' => 'Gagal menyimpan sampling: ' . $e->getMessage()];
    }
}

function sampling_reset(PDO $pdo): bool {
    sampling_ensure_tables($pdo);
    try {
        $stmt = $pdo->prepare("UPDATE sampling_periode SET status = 'selesai', reset_at = NOW() WHERE status = 'aktif'");
        $stmt->execute();
        return true;
    } catch (Exception $e) {
        error_log('sampling_reset: ' . $e->getMessage());
        return false;
    }
}

function wa_normalize_phone(?string $phone): ?string {
    if ($phone === null) return null;
    $digits = preg_replace('/\D+/', '', $phone);
    if ($digits === '' || strlen($digits) < 8) return null;
    if (strpos($digits, '0') === 0) {
        $digits = '62' . substr($digits, 1);
    } elseif (strpos($digits, '8') === 0) {
        $digits = '62' . $digits;
    }
    return $digits;
}

function wa_chat_url(?string $phone, string $text = ''): ?string {
    $n = wa_normalize_phone($phone);
    if (!$n) return null;
    $url = 'https://wa.me/' . $n;
    if ($text !== '') {
        $url .= '?text=' . rawurlencode($text);
    }
    return $url;
}
