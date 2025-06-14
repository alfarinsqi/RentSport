<?php
require_once '../config/db.php';

class FieldModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fungsi untuk mendapatkan semua lapangan yang tersedia
    public function getAvailableFields() {
        $query = "SELECT * FROM fields WHERE status = 'tersedia'";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Fungsi untuk mendapatkan lapangan berdasarkan ID
    public function getFieldById($id) {
        $query = "SELECT * FROM fields WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Fungsi untuk menambah lapangan baru
    public function createField($nama, $tipe, $harga_per_jam, $lokasi) {
        $query = "INSERT INTO fields (nama, tipe, harga_per_jam, lokasi, status) VALUES (?, ?, ?, ?, 'tersedia')";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$nama, $tipe, $harga_per_jam, $lokasi]);
    }

    // Fungsi untuk memperbarui status lapangan
    public function updateFieldStatus($id, $status) {
        $query = "UPDATE fields SET status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$status, $id]);
    }
}
?>
