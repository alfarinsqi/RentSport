<?php
require_once '../config/db.php';

class UserModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fungsi untuk mendapatkan pengguna berdasarkan ID
    public function getUserById($id) {
        $query = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Fungsi untuk mendapatkan pengguna berdasarkan email
    public function getUserByEmail($email) {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    // Fungsi untuk membuat pengguna baru
    public function createUser($nama, $email, $password) {
        $query = "INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, 'user')";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$nama, $email, $password]);
    }
}
?>
