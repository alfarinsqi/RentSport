<?php
require_once '../config/db.php';
require_once '../config/session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $field_id = $_POST['field_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Hitung jumlah jam sewa
    $start = new DateTime($start_time);
    $end = new DateTime($end_time);
    $jumlah_jam = ($end->getTimestamp() - $start->getTimestamp()) / 3600;

    // Panggil stored function untuk cek total biaya
    $stmt = $pdo->prepare("SELECT HitungTotalBiaya(?, ?) AS total_biaya");
    $stmt->execute([$field_id, $jumlah_jam]);
    $row = $stmt->fetch();
    $total_biaya = $row['total_biaya'];

    // Insert reservasi dengan prosedur (menggunakan transaction)
    $stmt = $pdo->prepare("CALL ReservasiLapangan(?, ?, ?, ?)");
    $stmt->execute([$user_id, $field_id, $start_time, $end_time]);

    echo "Reservasi berhasil! Total biaya: Rp " . number_format($total_biaya, 0, ',', '.');
}
?>
