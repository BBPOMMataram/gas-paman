<?php
date_default_timezone_set('Asia/Makassar');
// config/database.php
$host = 'localhost';
$db   = 'gas_paman';
$user = 'gaspaman'; 
$pass = 'Resso835'; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    // Perbaikan: gunakan -> untuk PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}