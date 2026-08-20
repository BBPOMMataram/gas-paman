<?php
/**
 * Helper geocoding: ubah teks alamat jadi koordinat (lat, lng) pakai
 * Nominatim (OpenStreetMap) - gratis, gak perlu API key.
 *
 * Karena data jalan/bangunan OSM di banyak desa NTB masih tipis, Nominatim
 * sering cuma bisa nemuin sampai level KECAMATAN/KABUPATEN (titik tengah
 * wilayah administratif, bukan alamat persis) - bukan bug, tapi keterbatasan
 * data peta itu sendiri. Supaya hasilnya sedekat mungkin ke desa yang benar:
 * - Query dipersempit (viewbox) ke sekitar kecamatan yang dipilih, biar
 *   Nominatim gak nyasar ke tempat lain yang namanya kebetulan mirip.
 * - Beberapa kandidat hasil dicek satu-satu, diutamakan yang nama desa/
 *   kelurahannya cocok dengan yang dipilih agen.
 * - Function ini juga ngasih tau precise=false kalau yang ketemu cuma
 *   level kecamatan/kabupaten (bukan desa persis), biar UI bisa kasih
 *   peringatan "wajib digeser manual" ke agen.
 *
 * PENTING - Kebijakan penggunaan Nominatim:
 * - Maksimal 1 request per detik. Kalau dipanggil dalam loop (lihat
 *   geocode-massal.php), WAJIB kasih jeda sleep(1) antar panggilan.
 * - Wajib kirim header User-Agent yang jelas (sudah ditangani di bawah).
 * - Kalau alamat gak ketemu / gagal, function ini return null - jangan
 *   bikin proses simpan data utama (agen/laporan) gagal gara-gara ini,
 *   selalu bungkus pemanggilannya biar silent-fail.
 */

function _geocoding_nominatim_query(string $query, array $viewbox): ?array {
    $url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
        'q'             => $query,
        'format'        => 'json',
        'limit'         => 5,
        'addressdetails'=> 1,
        'countrycodes'  => 'id',
        // Kotak batas wilayah: kiri,atas,kanan,bawah
        'viewbox'       => implode(',', $viewbox),
        'bounded'       => 1,
    ]);

    if (!function_exists('curl_init')) return null;

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 6,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_HTTPHEADER     => [
            'User-Agent: GAS-PAMAN-BBPOM-Mataram/1.0 (aplikasi internal edukasi BBPOM di Mataram)'
        ],
        CURLOPT_SSL_VERIFYPEER => true,
    ]);
    $response  = curl_exec($ch);
    $curlError = curl_errno($ch);
    curl_close($ch);

    if ($curlError || !$response) return null;
    $data = json_decode($response, true);
    return is_array($data) ? $data : null;
}

function geocode_alamat(?string $alamat): ?array {
    $alamat = trim((string)$alamat);
    if ($alamat === '') return null;

    // Hindari duplikasi "Nusa Tenggara Barat, Indonesia" kalau teks alamatnya
    // (hasil dari ntb_format_alamat) udah menyebutnya duluan.
    $query = $alamat;
    if (stripos($query, 'nusa tenggara barat') === false) {
        $query .= ', Nusa Tenggara Barat';
    }
    if (stripos($query, 'indonesia') === false) {
        $query .= ', Indonesia';
    }

    // Pecah alamat jadi kab/kec/desa (kalau formatnya dikenali) buat
    // mempersempit area pencarian & mengecek ketepatan hasil.
    $kab = ''; $kec = ''; $desa = '';
    if (!function_exists('ntb_parse_alamat_parts')) {
        $f = __DIR__ . '/ntb_wilayah_data.php';
        if (is_file($f)) require_once $f;
    }
    if (function_exists('ntb_parse_alamat_parts')) {
        $parts = ntb_parse_alamat_parts($alamat);
        $kab = $parts['kab'] ?? '';
        $kec = $parts['kec'] ?? '';
        $desa = $parts['desa'] ?? '';
    }

    $boxNtb = [115.6, -8.0, 119.4, -9.3];
    $boxKec = $boxNtb;
    if ($kec !== '' && function_exists('ntb_coords_from_wilayah')) {
        $c = ntb_coords_from_wilayah($kab, $kec);
        if (!empty($c['lat']) && !empty($c['lng'])) {
            // Kotak ±0.3 derajat (~30 km) di sekitar pusat kecamatan -
            // cukup buat nyakup seluruh kecamatan tapi tetap mempersempit
            // dibanding kotak sebesar provinsi.
            $boxKec = [$c['lng'] - 0.3, $c['lat'] + 0.3, $c['lng'] + 0.3, $c['lat'] - 0.3];
        }
    }

    $results = _geocoding_nominatim_query($query, $boxKec);
    // Kotak kecamatan kosong hasil (mis. kecamatan salah kebaca) -> coba
    // lagi dengan kotak seluruh NTB sebelum nyerah.
    if (empty($results) && $boxKec !== $boxNtb) {
        $results = _geocoding_nominatim_query($query, $boxNtb);
    }
    if (empty($results)) return null;

    // Cari kandidat yang nama desa/kelurahannya cocok dengan yang dipilih
    // agen - ini yang paling bisa dipercaya akurat sampai level desa.
    $best = null;
    $precise = false;
    if ($desa !== '') {
        $desaLower = mb_strtolower($desa);
        foreach ($results as $r) {
            $addr = $r['address'] ?? [];
            $fields = [
                $addr['village'] ?? '', $addr['suburb'] ?? '', $addr['town'] ?? '',
                $addr['hamlet'] ?? '', $addr['city_district'] ?? '', $addr['neighbourhood'] ?? '',
                $r['display_name'] ?? '',
            ];
            foreach ($fields as $f) {
                if ($f !== '' && mb_stripos(mb_strtolower($f), $desaLower) !== false) {
                    $best = $r;
                    $precise = true;
                    break 2;
                }
            }
        }
    }
    // Gak ada yang cocok persis ke desa -> pakai hasil teratas (paling
    // relevan menurut Nominatim), tapi tandai bukan hasil presisi desa.
    if (!$best) {
        $best = $results[0];
        $precise = false;
    }

    if (empty($best['lat']) || empty($best['lon'])) return null;

    return [
        'lat'     => (float)$best['lat'],
        'lng'     => (float)$best['lon'],
        'precise' => $precise,
    ];
}