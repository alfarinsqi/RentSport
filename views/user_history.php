<?php
// Ambil riwayat pemesanan pengguna
require_once '../config/db.php';

$query = "SELECT * FROM reservations WHERE user_id = ? AND approved_by_admin = 1";
$stmt = $pdo->prepare($query);
$stmt->execute([$_SESSION['user_id']]);
$reservations = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pemesanan | RentSport</title>
</head>
<body>

<div class="container">
    <h2 class="text-center">Riwayat Pemesanan</h2>

    <?php foreach ($reservations as $reservation): ?>
        <div class="reservation-item">
            <h4>Lapangan: <?php echo $reservation['field_name']; ?></h4>
            <p>Waktu Mulai: <?php echo $reservation['start_time']; ?></p>
            <p>Waktu Selesai: <?php echo $reservation['end_time']; ?></p>
            <p>Status: <?php echo $reservation['status']; ?></p>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
