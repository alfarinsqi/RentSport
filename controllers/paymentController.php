<?php
require_once '../config/db.php';
require_once '../config/session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reservation_id = $_POST['reservation_id'];
    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];

    // Masukkan transaksi ke database
    $query = "INSERT INTO transactions (reservation_id, amount, payment_method, payment_status) VALUES (?, ?, ?, 'paid')";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$reservation_id, $amount, $payment_method]);

    // Perbarui status pembayaran pada reservasi
    $query = "UPDATE reservations SET payment_status = 'paid' WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$reservation_id]);

    echo "Pembayaran berhasil, lapangan telah terpesan.";
}
?>
