<?php
require_once '../config/db.php';

class TransactionModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fungsi untuk membuat transaksi pembayaran
    public function createTransaction($reservation_id, $amount, $payment_method) {
        $query = "INSERT INTO transactions (reservation_id, amount, payment_method, payment_status) VALUES (?, ?, ?, 'pending')";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$reservation_id, $amount, $payment_method]);
    }

    // Fungsi untuk memperbarui status pembayaran transaksi
    public function updateTransactionStatus($id, $payment_status) {
        $query = "UPDATE transactions SET payment_status = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$payment_status, $id]);
    }

    // Fungsi untuk mendapatkan transaksi berdasarkan ID reservasi
    public function getTransactionByReservationId($reservation_id) {
        $query = "SELECT * FROM transactions WHERE reservation_id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$reservation_id]);
        return $stmt->fetch();
    }
}
?>
