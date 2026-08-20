<?php
/**
 * ID Agen otomatis berurutan: AG-001, AG-002, AG-003, ...
 * Urutan berdasarkan created_at (siapa daftar duluan = nomor lebih kecil).
 */

/**
 * Ambil nomor berikutnya = max numerik + 1 (setelah normalisasi).
 *
 * PENTING: dihitung dari SEMUA baris di tabel users, bukan cuma yang
 * role = 'agen'. Kolom agen_id punya UNIQUE KEY (uq_users_agen_id) yang
 * berlaku untuk seluruh tabel, dan admin bisa memindahkan seorang agen
 * ke role lain (mis. 'staff') lewat edit-agen.php sambil agen_id-nya
 * tetap melekat. Kalau perhitungan max cuma lihat role='agen', ID yang
 * masih "dipegang" oleh user ber-role lain itu dianggap kosong dan bisa
 * dikasih lagi ke agen baru → duplicate entry.
 */
function generate_next_agen_id(PDO $pdo): string {
    $stmt = $pdo->query("
        SELECT agen_id FROM users
        WHERE agen_id IS NOT NULL AND agen_id != ''
    ");
    $max = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (preg_match('/(\d+)/', trim($row['agen_id']), $m)) {
            $n = (int)$m[1];
            if ($n > $max) $max = $n;
        }
    }
    return 'AG-' . str_pad((string)($max + 1), 3, '0', STR_PAD_LEFT);
}

/**
 * Isi agen_id untuk agen yang MASIH KOSONG saja (mis. data lama sebelum
 * fitur ID agen ada). Agen yang sudah punya ID tidak pernah diubah —
 * ID agen sudah terlanjur dipakai/dicetak di ID Card & dokumen, jangan
 * sampai berubah diam-diam hanya karena ada pendaftar baru.
 * Return jumlah yang di-update.
 */
function backfill_agen_ids(PDO $pdo): int {
    $stmt = $pdo->query("
        SELECT id, agen_id
        FROM users
        WHERE role = 'agen'
          AND (agen_id IS NULL OR agen_id = '')
        ORDER BY created_at ASC, id ASC
    ");
    $agen = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!$agen) return 0;

    $upd = $pdo->prepare("UPDATE users SET agen_id = ? WHERE id = ?");
    $updated = 0;

    foreach ($agen as $a) {
        // Retry beberapa kali kalau ternyata ID hasil hitungan sudah
        // kepakai (mis. dua request nyaris bersamaan) — daripada bikin
        // seluruh registrasi gagal dengan error database mentah.
        $attempts = 0;
        while (true) {
            $agenId = generate_next_agen_id($pdo);
            try {
                $upd->execute([$agenId, $a['id']]);
                $updated++;
                break;
            } catch (PDOException $e) {
                $attempts++;
                // 23000 = integrity constraint violation (duplicate key)
                if ($e->getCode() == 23000 && $attempts < 10) {
                    continue; // hitung ulang, coba nomor berikutnya
                }
                throw $e;
            }
        }
    }
    return $updated;
}

function assign_agen_id_on_register(PDO $pdo, int $userId): string {
    // Isi ID untuk agen yang belum punya, lalu baca ID user ini.
    // Kalau user ini sudah kebagian ID (mis. panggilan berulang), pakai itu.
    backfill_agen_ids($pdo);
    $st = $pdo->prepare("SELECT agen_id FROM users WHERE id = ?");
    $st->execute([$userId]);
    $id = $st->fetchColumn();
    if ($id) return (string)$id;

    $agenId = generate_next_agen_id($pdo);
    $pdo->prepare("UPDATE users SET agen_id = ? WHERE id = ?")->execute([$agenId, $userId]);
    return $agenId;
}