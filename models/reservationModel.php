<?php
require_once '../config/db.php';

class ReservationModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fungsi untuk membuat reservasi baru
    public function createReservation($user_id, $field_id, $start_time, $end_time, $total_cost) {
        $query = "INSERT INTO reservations (user_id, field_id, start_time, end_time, total_cost, status, payment_status) VALUES (?, ?, ?, ?, ?, 'pending', 'pending')";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$user_id, $field_id, $start_time, $end_time, $total_cost]);
    }

    // Fungsi untuk mendapatkan reservasi berdasarkan ID
    public function getReservationById($id) {
        $query = "SELECT * FROM reservations WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Fungsi untuk mendapatkan semua reservasi berdasarkan ID pengguna
    public function getReservationsByUserId($user_id) {
        $query = "SELECT * FROM reservations WHERE user_id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    // Fungsi untuk memperbarui status reservasi
    public function updateReservationStatus($id, $status) {
        $query = "UPDATE reservations SET status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$status, $id]);
    }

    // Fungsi untuk memperbarui status pembayaran reservasi
    public function updatePaymentStatus($id, $payment_status) {
        $query = "UPDATE reservations SET payment_status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$payment_status, $id]);
    }
}
?>
