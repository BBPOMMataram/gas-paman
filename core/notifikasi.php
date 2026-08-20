<?php
/**
 * Sistem notifikasi multi-role GAS-PAMAN
 */

function notifikasi_ensure_table(PDO $pdo): void {
    static $done = false;
    if ($done) return;
    $done = true;
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS notifikasi (
                id INT NOT NULL AUTO_INCREMENT,
                user_id INT NOT NULL,
                role_target VARCHAR(20) NULL,
                tipe VARCHAR(40) NOT NULL DEFAULT 'umum',
                judul VARCHAR(200) NOT NULL,
                pesan TEXT NULL,
                link VARCHAR(255) NULL,
                ref_id INT NULL,
                is_read TINYINT(1) NOT NULL DEFAULT 0,
                created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_notif_user (user_id),
                KEY idx_notif_read (user_id, is_read),
                KEY idx_notif_created (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    } catch (Throwable $e) {
        error_log('notifikasi_ensure_table: ' . $e->getMessage());
    }
}

/**
 * Kirim notifikasi ke 1 user.
 */
function notifikasi_kirim(PDO $pdo, int $userId, string $tipe, string $judul, string $pesan = '', ?string $link = null, ?int $refId = null): bool {
    if ($userId <= 0) return false;
    try {
        notifikasi_ensure_table($pdo);
        $st = $pdo->prepare("INSERT INTO notifikasi (user_id, tipe, judul, pesan, link, ref_id) VALUES (?, ?, ?, ?, ?, ?)");
        return $st->execute([$userId, $tipe, $judul, $pesan, $link, $refId]);
    } catch (Throwable $e) {
        error_log('notifikasi_kirim: ' . $e->getMessage());
        return false;
    }
}

/**
 * Kirim ke semua user dengan role tertentu.
 */
function notifikasi_kirim_role(PDO $pdo, string $role, string $tipe, string $judul, string $pesan = '', ?string $link = null, ?int $refId = null): int {
    try {
        notifikasi_ensure_table($pdo);
        $st = $pdo->prepare("SELECT id FROM users WHERE role = ? AND status = 'aktif'");
        $st->execute([$role]);
        $ids = $st->fetchAll(PDO::FETCH_COLUMN);
        $n = 0;
        foreach ($ids as $uid) {
            if (notifikasi_kirim($pdo, (int)$uid, $tipe, $judul, $pesan, $link, $refId)) $n++;
        }
        return $n;
    } catch (Throwable $e) {
        error_log('notifikasi_kirim_role: ' . $e->getMessage());
        return 0;
    }
}

function notifikasi_count_unread(PDO $pdo, int $userId): int {
    try {
        notifikasi_ensure_table($pdo);
        $st = $pdo->prepare("SELECT COUNT(*) FROM notifikasi WHERE user_id = ? AND is_read = 0");
        $st->execute([$userId]);
        return (int)$st->fetchColumn();
    } catch (Throwable $e) {
        return 0;
    }
}

function notifikasi_list(PDO $pdo, int $userId, int $limit = 50, bool $unreadOnly = false): array {
    try {
        notifikasi_ensure_table($pdo);
        $sql = "SELECT * FROM notifikasi WHERE user_id = ?";
        if ($unreadOnly) $sql .= " AND is_read = 0";
        $sql .= " ORDER BY created_at DESC LIMIT " . (int)$limit;
        $st = $pdo->prepare($sql);
        $st->execute([$userId]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e) {
        return [];
    }
}

function notifikasi_get(PDO $pdo, int $id, int $userId): ?array {
    try {
        notifikasi_ensure_table($pdo);
        $st = $pdo->prepare("SELECT * FROM notifikasi WHERE id = ? AND user_id = ?");
        $st->execute([$id, $userId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    } catch (Throwable $e) {
        return null;
    }
}

function notifikasi_mark_read(PDO $pdo, int $id, int $userId): bool {
    try {
        notifikasi_ensure_table($pdo);
        $st = $pdo->prepare("UPDATE notifikasi SET is_read = 1 WHERE id = ? AND user_id = ?");
        return $st->execute([$id, $userId]);
    } catch (Throwable $e) {
        return false;
    }
}

function notifikasi_mark_all_read(PDO $pdo, int $userId): bool {
    try {
        notifikasi_ensure_table($pdo);
        $st = $pdo->prepare("UPDATE notifikasi SET is_read = 1 WHERE user_id = ? AND is_read = 0");
        return $st->execute([$userId]);
    } catch (Throwable $e) {
        return false;
    }
}

/** Icon class per tipe */
function notifikasi_icon(string $tipe): string {
    return match ($tipe) {
        'laporan_baru'     => 'fa-file-alt',
        'laporan_approve'  => 'fa-check-circle',
        'laporan_revisi'   => 'fa-exclamation-triangle',
        'test_selesai'     => 'fa-clipboard-check',
        'sertifikat'       => 'fa-certificate',
        'agen_baru'        => 'fa-user-plus',
        default            => 'fa-bell',
    };
}
