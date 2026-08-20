<?php
/**
 * Konfigurasi SMTP untuk pengiriman email (lupa password, notifikasi email).
 * Edit nilai di bawah sesuai kredensial email resmi BBPOM / hosting Anda.
 */
date_default_timezone_set('Asia/Makassar');

return [
    'host'       => 'mail.xlabscloud.com',
    'username'   => 'bensalem@xlabscloud.com',
    'password'   => 'YOUR_SMTP_PASSWORD', // TODO: ganti dengan password email asli
    'port'       => 587,
    'encryption' => 'tls', // 'tls' | 'ssl' | '' (tanpa enkripsi)
    'from_email' => 'bensalem@xlabscloud.com',
    'from_name'  => 'GAS-PAMAN BBPOM',
];
