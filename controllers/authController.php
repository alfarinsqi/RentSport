<?php
require_once '../config/db.php';  // Pastikan koneksi ke DB benar
require_once '../config/session.php';

// Fungsi untuk registrasi pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Mengamankan password

    // Memeriksa apakah email sudah terdaftar
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        echo "Email sudah terdaftar.";
    } else {
        $query = "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'user')";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$nama, $email, $password]);

        // Redirect ke halaman login setelah registrasi berhasil
        header('Location: ../views/auth/login.php');
        exit();
    }
}

// Fungsi untuk login pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Query untuk memeriksa apakah email sudah terdaftar
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Menyimpan informasi pengguna ke session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['nama'];
        $_SESSION['role'] = $user['role'];

        // Redirect ke dashboard sesuai dengan role
        if ($user['role'] == 'admin') {
            header('Location: ../views/admin_dashboard.php'); // Admin ke admin_dashboard.php
        } else {
            header('Location: ../views/dashboard.php'); // User biasa ke dashboard.php
        }
        exit();
    } else {
        echo "Email atau password salah!";
    }
}

// Fungsi untuk logout pengguna
if (isset($_GET['logout'])) {
    logout();
}

function logout() {
    session_unset(); // Menghapus semua session
    session_destroy(); // Menghancurkan session
    header("Location: login.php"); // Redirect ke halaman login
    exit();
}
?>
