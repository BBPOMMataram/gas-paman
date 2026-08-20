<?php
// Sesi tidak mati di tengah alur (mis. import soal otomatis yang lama)
// — override default server 24 menit jadi 24 jam per-request aplikasi.
ini_set('session.gc_maxlifetime', 86400);
session_start();

// Fungsi untuk mengecek login
function cek_login() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login");
        exit;
    }
}

// Fungsi untuk mengecek role admin
function cek_admin() {
    if ($_SESSION['role'] !== 'admin') {
        header("Location: dashboard");
        exit;
    }
}

// Fungsi untuk mengecek role staff atau admin (boleh kelola soal)
function cek_staff_atau_admin() {
    if (!in_array($_SESSION['role'], ['admin', 'staff'])) {
        header("Location: dashboard");
        exit;
    }
}

// Fungsi untuk mengecek role Kepala Balai (kabalai)
// Dipakai untuk halaman persetujuan & tanda tangan elektronik sertifikat
function cek_kabalai() {
    if ($_SESSION['role'] !== 'kabalai') {
        header("Location: dashboard");
        exit;
    }
}

// Fungsi untuk mengecek role admin ATAU kabalai
// Dipakai untuk halaman informasi (view-only) yang boleh dilihat kabalai
// selain admin, tanpa memberi akses aksi edit/hapus (itu tetap khusus admin)
function cek_admin_atau_kabalai() {
    if (!in_array($_SESSION['role'], ['admin', 'kabalai'])) {
        header("Location: dashboard");
        exit;
    }
}

// Fungsi untuk mengecek apakah profil agen sudah lengkap
// (dipakai untuk menentukan apakah ID Card sudah bisa otomatis dibuat/ditampilkan)
function profil_lengkap($user) {
    if (!$user) return false;

    $field_wajib = ['nama', 'agen_id', 'jenis_kelamin', 'nomor_hp', 'nama_instansi'];
    foreach ($field_wajib as $field) {
        if (empty($user[$field])) return false;
    }

    // Foto profil harus sudah diganti dari default.png
    if (empty($user['foto_profil']) || $user['foto_profil'] === 'default.png') return false;

    return true;
}
?>