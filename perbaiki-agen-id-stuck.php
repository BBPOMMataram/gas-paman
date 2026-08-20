<?php
/**
 * SCRIPT PERBAIKAN SEKALI PAKAI.
 *
 * Membersihkan baris users yang agen_id-nya nyangkut jadi string 'TEMP'
 * (akibat bug lama di register.php), lalu menomori ulang semua agen
 * secara berurutan (AG-001, AG-002, ...) berdasarkan created_at.
 *
 * CARA PAKAI:
 * 1. Upload file ini ke folder yang sama dengan register.php di hosting.
 * 2. Buka di browser: https://domain-anda.com/perbaiki-agen-id-stuck.php
 * 3. Baca hasilnya di layar.
 * 4. SETELAH BERHASIL, HAPUS FILE INI DARI SERVER. Jangan dibiarkan
 *    nyangkut, karena siapa pun yang tahu URL-nya bisa mengaksesnya.
 */

require_once 'config/database.php';
require_once __DIR__ . '/core/agen_id.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    // 1. Cari baris yang agen_id-nya masih 'TEMP' (bekas bug)
    $stmt = $pdo->query("SELECT id, nama, email, created_at FROM users WHERE agen_id = 'TEMP'");
    $stuck = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$stuck) {
        echo "Tidak ada baris dengan agen_id = 'TEMP'. Tidak ada yang perlu diperbaiki.\n";
    } else {
        echo "Ditemukan " . count($stuck) . " baris dengan agen_id = 'TEMP':\n";
        foreach ($stuck as $row) {
            echo "  - id={$row['id']} | {$row['nama']} | {$row['email']} | daftar: {$row['created_at']}\n";
        }

        // 2. Kosongkan dulu agen_id-nya (jadi NULL), supaya bisa ditangkap
        //    oleh backfill_agen_ids() dan dinomori ulang dengan benar.
        $pdo->exec("UPDATE users SET agen_id = NULL WHERE agen_id = 'TEMP'");
        echo "\nagen_id baris di atas sudah dikosongkan (NULL).\n";
    }

    // 3. Nomori ulang semua agen yang agen_id-nya kosong/NULL, berurutan
    //    sesuai created_at.
    $updated = backfill_agen_ids($pdo);
    echo "Backfill selesai. Jumlah baris yang baru diberi ID: {$updated}\n";

    // 4. Tampilkan daftar akhir agen + ID-nya, buat verifikasi manual.
    $final = $pdo->query("SELECT id, agen_id, nama, email FROM users WHERE role = 'agen' ORDER BY created_at ASC, id ASC")->fetchAll(PDO::FETCH_ASSOC);
    echo "\nDaftar agen saat ini:\n";
    foreach ($final as $row) {
        echo "  - {$row['agen_id']} | {$row['nama']} | {$row['email']}\n";
    }

    echo "\nSELESAI. Sekarang coba registrasi agen baru lagi — seharusnya sudah normal.\n";
    echo "JANGAN LUPA HAPUS FILE perbaiki-agen-id-stuck.php INI DARI SERVER SETELAH INI.\n";

} catch (Throwable $e) {
    echo "Terjadi error: " . $e->getMessage() . "\n";
}