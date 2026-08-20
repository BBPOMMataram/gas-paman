<?php
/**
 * core/soal_parser.php
 * Parser soal otomatis untuk import dari link / teks mentah.
 *
 * Mendukung format umum bank soal Indonesia:
 *   BAGIAN 1: SOAL UMUM
 *   1. Pertanyaan ...
 *      a. Opsi A
 *      b. Opsi B
 *      c. Opsi C
 *      d. Opsi D
 *      Jawaban: b
 *
 * Kategori: umum, komoditi_pangan, kosmetik, obat_bahan_alam, obat
 */

if (!defined('SOAL_KATEGORI_LIST')) {
    define('SOAL_KATEGORI_LIST', ['umum', 'komoditi_pangan', 'kosmetik', 'obat_bahan_alam', 'obat']);
}

define('SOAL_KATEGORI_LABEL', [
    'umum'            => 'Soal Umum',
    'komoditi_pangan' => 'Komoditi Pangan',
    'kosmetik'        => 'Kosmetik',
    'obat_bahan_alam' => 'Obat Bahan Alam & Suplemen',
    'obat'            => 'Obat',
]);

/** Ambil konten dari URL (cURL). Kembalikan string atau null jika gagal. */
function soal_ambil_url($url) {
    if (!preg_match('#^https?://#i', $url)) return null;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS      => 8,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36',
        CURLOPT_ENCODING       => '',
    ]);
    $body = curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // Handle $ch otomatis ditutup oleh PHP >= 8 (curl_close() deprecated sejak 8.5)

    if ($body === false || $body === '' || $code >= 400) {
        error_log('[soal_ambil_url] Gagal mengambil: ' . $url . ' | http=' . $code . ' | curl=' . curl_error($ch));
        return null;
    }
    return $body;
}

/** Lokasi file cache teks soal (di luar webroot, TTL pendek). */
function soal_cache_file($url) {
    $dir = rtrim(sys_get_temp_dir(), '/\\') . '/gas-paman-soal-cache';
    if (!is_dir($dir)) @mkdir($dir, 0775, true);
    return $dir . '/' . md5($url) . '.txt';
}

/** Cek apakah respons Google Drive berupa halaman HTML (konfirmasi/error), bukan isi file. */
function soal_html_konfirmasi($body) {
    if ($body === null || $body === '') return false;
    $head = strtolower(substr(ltrim($body), 0, 300));
    return strpos($head, '<!doctype') !== false
        || strpos($head, '<html') !== false
        || strpos($head, 'downloadform') !== false;
}

/**
 * Ambil teks soal dari sebuah link, dengan cache file 10 menit
 * supaya import ulang dengan link yang sama tidak mengunduh lagi.
 * $refresh = true -> abaikan cache (untuk tombol "Muat ulang tanpa cache").
 */
function soal_ambil_dari_link($url, $refresh = false) {
    $cacheFile = soal_cache_file($url);
    if ($refresh) {
        @unlink($cacheFile);
    } elseif (is_file($cacheFile) && (time() - filemtime($cacheFile)) < 600) {
        $isi = @file_get_contents($cacheFile);
        // Jangan pernah pakai cache sampah (HTML konfirmasi dari versi lama).
        if ($isi !== false && trim($isi) !== '' && !soal_html_konfirmasi($isi)) return $isi;
    }

    $mentah = soal_ambil_dari_link_langsung($url);
    if ($mentah !== null && !soal_html_konfirmasi($mentah)) {
        @file_put_contents($cacheFile, $mentah);
    } else {
        error_log('[soal_ambil_dari_link] Gagal unduh atau respons HTML: ' . $url);
        $mentah = null;
    }
    return $mentah;
}

/**
 * Unduh teks soal dari link (tanpa cache).
 * Mendukung Google Docs (ekspor txt) & Google Drive (file publik).
 * Kembalikan string teks mentah atau null.
 */
function soal_ambil_dari_link_langsung($url) {
    // 1. Google Docs: ekspor txt. Kalau gagal, langsung berhenti —
    //    jangan unduh halaman editor yang berat & tetap tidak bisa diparse.
    if (preg_match('#docs\.google\.com/document/(?:u/\d+/)?d/([a-zA-Z0-9_-]+)#', $url, $m)) {
        $txt = soal_ambil_url('https://docs.google.com/document/d/' . $m[1] . '/export?format=txt');
        if ($txt !== null && soal_html_konfirmasi($txt)) {
            error_log('[soal_ambil_dari_link_langsung] Docs mengembalikan halaman HTML (tidak publik?): ' . $url);
            return null; // jangan di-cache
        }
        return $txt;
    }

    // 2. Google Drive file: coba endpoint usercontent langsung (tanpa rantai
    //    redirect uc), lalu fallback ke uc.
    if (preg_match('#drive\.google\.com/(?:file/d/|open\?id=|uc\?id=|uc\?export=download&id=)([a-zA-Z0-9_-]+)#', $url, $m)) {
        $id  = $m[1];
        $txt = soal_ambil_url('https://drive.usercontent.google.com/download?id=' . $id . '&export=download&confirm=t');
        if ($txt !== null && !soal_html_konfirmasi($txt)) return $txt;
        $txt = soal_ambil_url('https://drive.google.com/uc?export=download&id=' . $id);
        if ($txt !== null && !soal_html_konfirmasi($txt)) return $txt;
        error_log('[soal_ambil_dari_link_langsung] Drive mengembalikan halaman HTML (tidak publik?): ' . $url);
        return null;
    }

    // 3. Link biasa: tolak respons yang ternyata halaman HTML (mis. halaman
    //    login/unduh dari penyedia file) supaya tidak di-cache sebagai sampah.
    $txt = soal_ambil_url($url);
    if ($txt !== null && soal_html_konfirmasi($txt)) {
        error_log('[soal_ambil_dari_link_langsung] Link biasa mengembalikan halaman HTML: ' . $url);
        return null;
    }
    return $txt;
}

/** Ubah HTML mentah menjadi teks polos per baris. */
function soal_teks_bersih($html) {
    // Buang script & style
    $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $html);
    $html = preg_replace('#<style\b[^>]*>.*?</style>#is', '', $html);

    // Tag block -> baris baru
    $html = preg_replace('#<(br|/p|/div|/li|/tr|/h[1-6]|/td)\s*/?>#i', "\n", $html);
    $html = preg_replace('#</(p|div|li|tr|h[1-6]|td)>#i', "\n", $html);

    // Sisa tag dibuang
    $html = strip_tags($html);

    // Entitas HTML
    $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Karakter non-breaking & whitespace
    $html = str_replace(["\xc2\xa0", "\r"], [' ', "\n"], $html);

    // Baris: trim, buang baris kosong ganda
    $lines = [];
    foreach (preg_split('/\n+/', $html) as $line) {
        $line = trim($line);
        if ($line === '') continue;
        // Angka 1-5 jadi huruf tidak diganggu; normalkan spasi ganda
        $lines[] = preg_replace('/[ \t]+/', ' ', $line);
    }
    return $lines;
}

/** Cocokkan header bagian dengan salah satu kategori. */
function soal_deteksi_kategori_header($line) {
    $l = mb_strtolower($line);

    // Urutan penting: "obat bahan alam" harus dicek sebelum "obat"
    if (preg_match('/(obat\s+bahan\s+alam|obat\s+tradisional|suplemen\s+(?:kesehatan|makanan)|jamu)/i', $l)) {
        return 'obat_bahan_alam';
    }
    if (preg_match('/(komoditi\s+pangan|komoditas\s+pangan|keamanan\s+pangan|\bpangan\b)/i', $l)) {
        return 'komoditi_pangan';
    }
    if (preg_match('/\bkosmetik\b/i', $l)) {
        return 'kosmetik';
    }
    if (preg_match('/\bobat\b/i', $l)) {
        return 'obat';
    }
    if (preg_match('/\bumum\b/i', $l)) {
        return 'umum';
    }
    return null;
}

/**
 * Deteksi baris header bagian test: "PRE TEST" / "POST TEST" / "* PRE TEST" /
 * "BAGIAN PRE TEST" / "PRE TEST — SOAL UMUM". Return 'pre_test' / 'post_test' / null.
 * Prosa biasa yang menyebut pre-test (huruf campuran, kalimat panjang) TIDAK dianggap header.
 */
function soal_deteksi_jenis_header($line) {
    if (!preg_match('/(?:pre[-_ ]?test|post[-_ ]?test)/i', $line)) return null;

    $intim = preg_replace('/^[*\s]+/', '', $line);

    // (a) Isinya HANYA frasa jenis test
    $murni = (bool)preg_match('/^(?:pre|post)[-_ ]?test\s*$/i', $intim) && mb_strlen($intim) <= 30;
    // (b) Berawalan bagian/section/bab/materi/kelompok/tes/test
    $denganAwalan = (bool)preg_match('/^\s*(?:bagian|section|bab|materi|kelompok|tes|test)\b/i', $line);
    // (c) Baris pendek HURUF KAPITAL (pola header kategori + en/em-dash)
    $kapitalPendek = (bool)(preg_match('/^\s*\*?\s*[A-Z0-9][A-Z0-9\s\/&()_\-–—]{2,59}$/u', $line)
        && !preg_match('/[.!?…,:;]$/u', $line) && mb_strlen($line) <= 60);

    if (!$murni && !$denganAwalan && !$kapitalPendek) return null;

    // "pre" menang bila dua-duanya muncul di satu baris (jarang)
    return preg_match('/pre[-_ ]?test/i', $line) ? 'pre_test' : 'post_test';
}

/** Klasifikasi soal tanpa header, lewat kata kunci pada teks pertanyaan. */
function soal_deteksi_kategori_konten($teks) {
    $l = mb_strtolower($teks);
    if (preg_match('/(jamu|herbal|obat tradisional|suplemen|vitamin|obat bahan alam)/i', $l)) {
        return 'obat_bahan_alam';
    }
    if (preg_match('/(kosmetik|krim|bedak|lipstik|parfum|makeup|pemutih|skincare|pelembap|tabir surya)/i', $l)) {
        return 'kosmetik';
    }
    if (preg_match('/(pangan|beras|sayur|buah|makanan|minuman|bumbu|kemasan pangan|pengawet|pewarna)/i', $l)) {
        return 'komoditi_pangan';
    }
    if (preg_match('/(obat|apotek|resep|antibiotik|dosis|farmasi|generik)/i', $l)) {
        return 'obat';
    }
    return 'umum';
}

/** Baris non-kosong berikutnya setelah indeks $i diawali penanda opsi (huruf a-e atau angka 1)? */
function soal_baris_berikut_opsi($lines, $i) {
    for ($j = $i + 1, $n = count($lines); $j < $n; $j++) {
        $t = trim($lines[$j]);
        if ($t === '') continue;
        return (bool)preg_match('/^\s*(?:[a-eA-E]\s*[.)]|1\s*[.)])/', $t);
    }
    return false;
}

/** Baris non-kosong berikutnya setelah indeks $i diawali angka $angka + . atau )? */
function soal_baris_berikut_angka($lines, $i, $angka) {
    for ($j = $i + 1, $n = count($lines); $j < $n; $j++) {
        $t = trim($lines[$j]);
        if ($t === '') continue;
        return (bool)preg_match('/^\s*' . (int)$angka . '\s*[.)]/', $t);
    }
    return false;
}

/**
 * Pisahkan bagian "KUNCI JAWABAN" terpisah di akhir dokumen dari baris soal.
 * Bagian valid bila header ("KUNCI JAWABAN" / "KUNCI" / "JAWABAN", baris utuh
 * tanpa huruf kunci) diikuti minimal 1 entri bernomor ("1. B" / "1) B")
 * ATAU minimal 2 huruf telanjang berurutan ("B", "C", ...).
 * Return ['baris' => baris tanpa bagian kunci, 'kunci' => [nomorSoal => idxOpsi], 'bare' => [idxOpsi, ...]].
 */
function soal_ekstrak_bagian_kunci(array $lines): array {
    $n          = count($lines);
    $mulai      = -1;  // index header bagian yang sedang dibaca
    $entri      = -1;  // index baris entri terakhir pada bagian ini
    $kunci      = [];  // nomor soal => indeks opsi (0 = a)
    $bare       = [];  // huruf telanjang berurutan
    $mulaiValid = -1;  // header bagian valid (untuk di-strip)
    $entriValid = -1;
    $kunciFinal = [];
    $bareFinal  = [];

    $simpanValid = function () use (&$mulai, &$entri, &$kunci, &$bare, &$mulaiValid, &$entriValid, &$kunciFinal, &$bareFinal) {
        if (empty($kunci) && count($bare) < 2) return; // tidak valid
        $mulaiValid = $mulai;
        $entriValid = $entri;
        $kunciFinal = $kunci;
        $bareFinal  = $bare;
    };

    for ($i = 0; $i < $n; $i++) {
        $l = trim($lines[$i]);
        if ($l === '') continue;

        // Header bagian baru? Baris UTUH hanya frasa kunci (opsional : . -)
        if (preg_match('/^(?:kunci\s*jawaban|kunci|jawaban)\s*[:.\-–—]?\s*$/i', $l) && mb_strlen($l) <= 30) {
            $simpanValid(); // simpan bagian sebelumnya bila valid
            $mulai = $i;
            $entri = -1;
            $kunci = [];
            $bare  = [];
            continue;
        }
        if ($mulai === -1) continue;

        // Entri bernomor: "1. B" / "1) B" / "1 B"
        if (preg_match('/^(\d{1,2})\s*[.)]?\s*([a-eA-E])\.?\s*$/', $l, $m)) {
            $kunci[(int)$m[1]] = ord(strtolower($m[2])) - 97;
            $entri = $i;
            continue;
        }
        // Entri huruf telanjang: "B" / "B."
        if (preg_match('/^([a-eA-E])\.?\s*$/', $l, $m)) {
            $bare[] = ord(strtolower($m[1])) - 97;
            $entri  = $i;
            continue;
        }
        // Baris bukan entri -> bagian selesai
        $simpanValid();
        $mulai = -1;
    }
    $simpanValid(); // bagian di ujung dokumen

    if ($mulaiValid === -1) {
        return ['baris' => $lines, 'kunci' => [], 'bare' => []];
    }

    $barisBaru = [];
    foreach ($lines as $i => $line) {
        if ($i >= $mulaiValid && $i <= $entriValid) continue;
        $barisBaru[] = $line;
    }
    return ['baris' => $barisBaru, 'kunci' => $kunciFinal, 'bare' => $bareFinal];
}

/**
 * Parse daftar baris teks menjadi struktur soal:
 * [
 *   ['kategori' => 'umum', 'teks' => '...', 'opsi' => [['teks' => '...', 'benar' => true], ...]],
 *   ...
 * ]
 */
function soal_parse_baris($lines) {
    $soalList     = [];
    $kategori     = 'umum'; // kategori aktif (terakhir ditemukan dari header)
    $jenisAktif   = null;   // jenis test aktif: 'pre_test' / 'post_test' / null
    $cur          = null;   // soal yang sedang dibangun
    $pakaiHeader  = false;  // dokumen memakai header bagian?
    $stemPending  = [];     // kalimat awal soal berikutnya (stem multi-baris)

    // Pisahkan bagian "KUNCI JAWABAN" terpisah di akhir dokumen
    $ekstrak     = soal_ekstrak_bagian_kunci($lines);
    $lines       = $ekstrak['baris'];
    $kunciBagian = $ekstrak['kunci'];
    $kunciBare   = $ekstrak['bare'];

    for ($i = 0, $n = count($lines); $i < $n; $i++) {
        $line = trim($lines[$i]);
        if ($line === '') continue;

        // 1. Header bagian baru? Dua format didukung:
        //    - "Bagian 1: Soal Umum" / "Section..." (kata pembuka umum)
        //    - "* PERTANYAAN UMUM" / "* KOMODITI OBAT" (baris pendek HURUF KAPITAL)
        //    Header jenis test ("PRE TEST" / "POST TEST" / kombinasinya) juga
        //    dikonsumsi di sini — selalu dianggap header bila terdeteksi.
        $katHeader   = soal_deteksi_kategori_header($line);
        $jenisHeader = soal_deteksi_jenis_header($line);
        $isHeader    = ($jenisHeader !== null);
        if (!$isHeader && $katHeader !== null) {
            if (preg_match('/^\s*(?:bagian|section|bab|materi|kelompok|tes|test)\b/i', $line)) {
                $isHeader = true;
            } elseif (preg_match('/^\s*\*?\s*[A-Z0-9][A-Z0-9\s\/&()_-]{2,59}$/u', $line)
                      && !preg_match('/[.!?…,:;]$/u', $line)) {
                $isHeader = true;
            }
        }
        if ($isHeader) {
            if ($jenisHeader !== null) $jenisAktif = $jenisHeader;
            if ($katHeader !== null) {
                $kategori    = $katHeader;
                $pakaiHeader = true;
            }
            // Stem tertunda sebelum header: gabungkan ke soal sebelumnya
            if ($cur !== null && $stemPending) {
                $cur['teks'] .= ' ' . implode(' ', $stemPending);
            }
            $stemPending = [];
            continue;
        }

        // 2. Awal soal tanpa nomor: soal aktif sudah punya opsi, baris ini bukan
        //    opsi/kunci (dan diakhiri tanda baca), lalu baris berikutnya adalah
        //    penanda opsi -> tutup soal lama & mulai soal baru.
        if ($cur !== null
            && count($cur['opsi']) >= 2
            && !preg_match('/^\s*(?:[a-eA-E]\s*[.)]|\d{1,2}\s*[.)])/', $line)
            && !preg_match('/^(?:kunci\s*jawaban|jawaban|kunci|answer)\s*[:.]\s*[a-eA-E]\b/i', $line)
            && preg_match('/[.!?…:;]$/u', $line)
            && soal_baris_berikut_opsi($lines, $i)) {
            $soalList[] = $cur;
            $cur = [
                'kategori'       => $kategori,
                'jenis'          => $jenisAktif,
                'teks'           => ($stemPending ? implode(' ', $stemPending) . ' ' : '') . $line,
                'opsi'           => [],
                'jawaban'        => null,
                'nomor'          => null,
                'opsi_angka'     => false,
                'angka_terakhir' => 0,
            ];
            $stemPending = [];
            continue;
        }

        // 3. Nomor soal: "1. teks..." / "1) teks..." / "Soal 1: teks..."
        //    atau opsi bernomor (1. 2. 3. 4.) pada soal tanpa opsi huruf.
        if (preg_match('/^\s*(soal\s+)?(\d{1,2})\s*[.)]\s+(.+)$/i', $line, $m)) {
            $angka = (int)$m[2];

            // 3a. Lanjutan opsi bernomor (2., 3., 4.)
            if ($cur !== null && !empty($cur['opsi_angka'])
                && $cur['angka_terakhir'] < 5 && $angka === $cur['angka_terakhir'] + 1) {
                $cur['opsi'][] = ['teks' => trim($m[3]), 'benar' => false];
                $cur['angka_terakhir'] = $angka;
                continue;
            }
            // 3b. Awal opsi bernomor: "1." dan baris berikutnya "2."
            if ($cur !== null && $m[1] === '' && count($cur['opsi']) === 0
                && $angka === 1 && soal_baris_berikut_angka($lines, $i, 2)) {
                $cur['opsi_angka']     = true;
                $cur['angka_terakhir'] = 1;
                $cur['opsi'][] = ['teks' => trim($m[3]), 'benar' => false];
                continue;
            }

            // 3c. Nomor soal baru
            if ($cur !== null) $soalList[] = $cur;
            $cur = [
                'kategori'       => $kategori,
                'jenis'          => $jenisAktif,
                'teks'           => ($stemPending ? implode(' ', $stemPending) . ' ' : '') . trim($m[3]),
                'opsi'           => [],
                'jawaban'        => null, // indeks opsi yang benar
                'nomor'          => $angka,
                'opsi_angka'     => false,
                'angka_terakhir' => 0,
            ];
            $stemPending = [];
            continue;
        }

        // 4. Opsi huruf: "a. teks..." / "A) teks..." / "*b. teks*" (bold = kunci)
        if ($cur !== null && preg_match('/^\s*(\*{1,2})?\s*([a-eA-E])\s*[.)]\s+(.+)$/', $line, $m)) {
            // Stem tertunda yang diikuti opsi = baris lanjutan opsi yang terpotong
            if ($stemPending) {
                $cur['teks'] .= ' ' . implode(' ', $stemPending);
                $stemPending = [];
            }
            $huruf = strtolower($m[2]);
            $teks  = trim($m[3]);
            // Tandai jawaban benar jika ada penanda:
            //  - *b. opsi* / **b) opsi** : bintang di depan huruf opsi
            //  - a. *teks* (format lama) : bintang melingkupi teks opsi
            $benar = ($m[1] !== '');
            if (!$benar && preg_match('/^\*{1,2}(.+)\*{1,2}$/', $teks, $mm)) {
                $teks  = trim($mm[1]);
                $benar = true;
            } else {
                $teks = preg_replace('/\s*\*+$/', '', $teks); // buang bintang penutup "*b. opsi*"
            }
            $idx = count($cur['opsi']);
            $cur['opsi'][] = ['teks' => $teks, 'benar' => $benar];
            if ($benar) $cur['jawaban'] = $idx;
            continue;
        }

        // 5. Baris kunci: "Jawaban: b" / "Kunci: B" / "Kunci Jawaban: c"
        if ($cur !== null && preg_match('/^(?:kunci\s*jawaban|jawaban|kunci|answer)\s*[:.]?\s*([a-eA-E])\b/i', $line, $m)) {
            if ($stemPending) {
                $cur['teks'] .= ' ' . implode(' ', $stemPending);
                $stemPending = [];
            }
            $cur['jawaban'] = ord(strtolower($m[1])) - 97; // a -> 0
            continue;
        }

        // 5b. "Jawaban:" / "Kunci:" di baris sendiri, huruf kunci di baris berikutnya
        if ($cur !== null && preg_match('/^(?:kunci\s*jawaban|jawaban|kunci|answer)\s*[:.]\s*$/i', $line)) {
            $next = null;
            for ($j = $i + 1; $j < $n; $j++) {
                $t = trim($lines[$j]);
                if ($t !== '') { $next = $t; break; }
            }
            if ($next !== null && preg_match('/^([a-eA-E])\.?\s*$/', $next, $m2)) {
                if ($stemPending) {
                    $cur['teks'] .= ' ' . implode(' ', $stemPending);
                    $stemPending = [];
                }
                $cur['jawaban'] = ord(strtolower($m2[1])) - 97;
                continue; // baris huruf tunggal akan dilewati branch 6 (mb_strlen <= 2)
            }
        }

        // 6. Baris lain yang bukan header & ada soal aktif: anggap lanjutan teks soal.
        //    Kalau soal aktif sudah lengkap (punya opsi) dan baris diakhiri tanda baca,
        //    kemungkinan itu kalimat awal soal berikutnya -> tahan di stemPending dulu.
        if ($cur !== null && mb_strlen($line) > 2) {
            if (count($cur['opsi']) >= 2 && preg_match('/[.!?…:;]$/u', $line)) {
                $stemPending[] = $line;
            } else {
                $cur['teks'] .= ' ' . $line;
            }
        }
    }
    if ($cur !== null) {
        if ($stemPending) $cur['teks'] .= ' ' . implode(' ', $stemPending);
        $soalList[] = $cur;
    }

    // Buang soal tanpa opsi (sebelum merge kunci supaya mapping nomor tidak bergeser)
    $soalList = array_values(array_filter($soalList, function ($s) {
        return count($s['opsi']) >= 2;
    }));

    // Merge kunci dari bagian "KUNCI JAWABAN" terpisah:
    // entri bernomor diisi by nomor soal; huruf telanjang diisi berurutan.
    // Kunci inline per-soal menang — hanya mengisi soal yang belum punya kunci.
    foreach ($soalList as &$s) {
        if ($s['jawaban'] === null && $s['nomor'] !== null && isset($kunciBagian[$s['nomor']])) {
            $j = (int)$kunciBagian[$s['nomor']];
            if ($j >= 0 && $j < count($s['opsi'])) $s['jawaban'] = $j;
        }
    }
    unset($s);
    $idx = 0;
    foreach ($soalList as &$s) {
        if ($s['jawaban'] === null && isset($kunciBare[$idx])) {
            $j = (int)$kunciBare[$idx];
            if ($j >= 0 && $j < count($s['opsi'])) $s['jawaban'] = $j;
        }
        $idx++;
    }
    unset($s);

    // Rapikan: jawaban dari penanda; kategori fallback via konten
    foreach ($soalList as &$s) {
        if ($s['jawaban'] === null) {
            foreach ($s['opsi'] as $i => $o) {
                if (!empty($o['benar'])) { $s['jawaban'] = $i; break; }
            }
        }
        // Normalisasi: hanya satu opsi benar
        if ($s['jawaban'] !== null) {
            foreach ($s['opsi'] as $i => &$o) {
                $o['benar'] = ($i === $s['jawaban']);
            }
            unset($o);
        }
        // Kategori tak terdeteksi header -> deteksi dari isi pertanyaan.
        // Hanya bila dokumen TIDAK memakai header bagian sama sekali,
        // supaya kategori yang sudah ditetapkan header tidak tertimpa.
        if (!$pakaiHeader && $s['kategori'] === 'umum') {
            $s['kategori'] = soal_deteksi_kategori_konten($s['teks']);
        }
    }
    unset($s);

    return $soalList;
}

/**
 * Titik masuk utama: dari teks mentah (HTML atau polos) -> daftar soal.
 * Return array soal atau [] bila gagal parse.
 */
function soal_parse_teks($mentah) {
    if (!is_string($mentah) || trim($mentah) === '') return [];

    // Deteksi apakah konten masih HTML
    if (preg_match('/<\s*(html|body|div|p|br|table|li)[^>]*>/i', $mentah)) {
        $lines = soal_teks_bersih($mentah);
    } else {
        $lines = soal_teks_bersih($mentah);
    }

    return soal_parse_baris($lines);
}

/** Ringkasan jumlah soal per kategori (untuk tampilan preview). */
function soal_rekap_kategori($soalList) {
    $rekap = array_fill_keys(SOAL_KATEGORI_LIST, 0);
    foreach ($soalList as $s) {
        if (isset($rekap[$s['kategori']])) $rekap[$s['kategori']]++;
    }
    return $rekap;
}

/**
 * Validasi komposisi bank soal: minimal 3 soal per kategori (total minimal 15).
 * Return daftar masalah ([label => jumlah]) — array kosong berarti valid.
 */
function soal_validasi_komposisi(array $rekap): array {
    $masalah = [];
    foreach (SOAL_KATEGORI_LIST as $kat) {
        if (($rekap[$kat] ?? 0) < 3) {
            $masalah[] = SOAL_KATEGORI_LABEL[$kat] . ' (' . ($rekap[$kat] ?? 0) . '/3)';
        }
    }
    return $masalah;
}

/**
 * Pastikan tabel hasil_test_soal ada (set 15 soal acak per percobaan test).
 * Dipanggil di submit-test.php, detail-hasil-test.php, dan halaman hapus.
 */
function hasil_test_soal_ensure_table(PDO $pdo): void {
    static $checked = false;
    if ($checked) return;
    $checked = true;
    try {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS hasil_test_soal (
                id INT NOT NULL AUTO_INCREMENT,
                hasil_test_id INT NOT NULL,
                pertanyaan_id INT NOT NULL,
                urutan INT NOT NULL,
                PRIMARY KEY (id),
                KEY idx_hts_hasil (hasil_test_id),
                KEY idx_hts_pertanyaan (pertanyaan_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    } catch (Exception $e) {
        error_log('hasil_test_soal_ensure_table: ' . $e->getMessage());
    }
}
