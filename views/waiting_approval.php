<?php
require_once '../config/session.php';
require_once '../config/db.php';

// Cek apakah pengguna sudah login
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Ambil ID reservasi terbesar dari database
$query = "SELECT MAX(id) AS max_id FROM reservations";
$stmt = $pdo->prepare($query);
$stmt->execute();
$result = $stmt->fetch();

// Dapatkan ID terbesar
$reservation_id = $result['max_id'];

// Query untuk mendapatkan data reservasi berdasarkan ID terbesar
$query = "SELECT r.*, t.payment_status, t.amount, t.payment_date, f.nama AS field_name 
          FROM reservations r 
          LEFT JOIN transactions t ON r.id = t.reservation_id 
          LEFT JOIN fields f ON r.field_id = f.id 
          WHERE r.id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$reservation_id]);
$reservation = $stmt->fetch();

if (!$reservation) {
    echo "Reservasi tidak ditemukan.";
    exit();
}

// Pastikan bahwa approved_by_admin ada dan memiliki nilai
$approved_by_admin = isset($reservation['approved_by_admin']) ? $reservation['approved_by_admin'] : 0;

// Pastikan waktu mulai dan selesai valid
$start_time = isset($reservation['start_time']) ? date('d-m-Y H:i', strtotime($reservation['start_time'])) : 'Belum ditentukan';
$end_time = isset($reservation['end_time']) ? date('d-m-Y H:i', strtotime($reservation['end_time'])) : 'Belum ditentukan';

// Pastikan field_name tidak null atau kosong
$field_name = !empty($reservation['field_name']) ? ucfirst($reservation['field_name']) : 'Lapangan Tidak Ditemukan';

require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pembayaran | RentSport</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Status Pembayaran Reservasi Lapangan</h2>

    <!-- Menampilkan status pemesanan -->
    <div class="text-center">
        <?php if ($approved_by_admin == 1): ?>
            <h4 class="text-success">Pembayaran Disetujui!</h4>
            <p class="lead">Reservasi Anda telah disetujui oleh admin.</p>
            <p>Lapangan: <?php echo $field_name; ?></p>
            <p>Waktu: <?php echo $start_time; ?> - <?php echo $end_time; ?></p>
            <p>Status: Berhasil</p>
        <?php elseif ($approved_by_admin == 2): ?>
            <h4 class="text-danger">Pembayaran Gagal!</h4>
            <p class="lead">Reservasi Anda tidak disetujui oleh admin.</p>
            <p>Status: Gagal</p>
        <?php else: ?>
            <h4 class="text-warning">Menunggu Persetujuan Admin</h4>
            <p class="lead">Pembayaran Anda sedang menunggu persetujuan dari admin.</p>
        <?php endif; ?>
    </div>

</div>

<?php require_once 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
