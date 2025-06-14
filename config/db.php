<?php
$host = 'localhost'; // Ganti dengan host database Anda
$dbname = 'rentsport'; // Nama database Anda
$username = 'root'; // Username database Anda
$password = ''; // Password database Anda

try {
    // Membuat koneksi ke database menggunakan PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    // Set mode error ke exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Cek apakah koneksi berhasil
    // echo "Koneksi berhasil!";
} catch (PDOException $e) {
    // Jika gagal, tampilkan pesan error
    die("Koneksi gagal: " . $e->getMessage());
}
?>
