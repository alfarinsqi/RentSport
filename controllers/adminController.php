<?php
require_once '../config/db.php';
require_once '../config/session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reservation_id'])) {
    $reservation_id = $_POST['reservation_id'];

    // Konfirmasi dengan procedure
    $stmt = $pdo->prepare("CALL ConfirmReservation(?)");
    $stmt->execute([$reservation_id]);

    echo "Reservasi berhasil dikonfirmasi!";
}
?>
