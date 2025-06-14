<?php
session_start();

// Fungsi untuk memulai session pengguna
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Fungsi untuk mendapatkan informasi pengguna
function getUsername() {
    return isset($_SESSION['username']) ? $_SESSION['username'] : null;
}

// Fungsi logout sudah ada di authController.php, jadi tidak perlu dideklarasikan lagi di sini.
?>
