<?php
/**
 * Endpoint AJAX publik: sama seperti ajax-geocode.php, tapi dipakai khusus
 * di halaman REGISTRASI (register.php) yang diakses SEBELUM ada sesi login,
 * jadi endpoint ini SENGAJA tidak memanggil cek_login().
 *
 * Cuma proxy tipis ke geocode_alamat() (Nominatim) - tidak membaca/menulis
 * data user manapun, jadi aman diakses publik. Endpoint ajax-geocode.php
 * yang lama (wajib login) tetap dipakai apa adanya di halaman-halaman
 * internal (profil, tambah/edit catatan, edit agen).
 */
require_once 'core/geocoding.php';

header('Content-Type: application/json');

$alamat = trim($_POST['alamat'] ?? $_GET['alamat'] ?? '');
if ($alamat === '') {
    echo json_encode(['ok' => false, 'message' => 'Alamat kosong']);
    exit;
}

$geo = geocode_alamat($alamat);
if (!$geo) {
    echo json_encode(['ok' => false, 'message' => 'Alamat tidak ditemukan, geser pin manual di peta']);
    exit;
}

echo json_encode([
    'ok'      => true,
    'lat'     => $geo['lat'],
    'lng'     => $geo['lng'],
    'precise' => $geo['precise'] ?? false,
]);
