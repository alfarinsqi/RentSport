<?php
require_once '../config/session.php';
require_once '../config/db.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['reservation_id'])) {
    $reservation_id = $_GET['reservation_id'];

    // Query untuk mendapatkan data reservasi yang sudah disetujui admin
    $query = "SELECT r.*, f.nama AS field_name, t.payment_status
              FROM reservations r
              LEFT JOIN fields f ON r.field_id = f.id
              LEFT JOIN transactions t ON r.id = t.reservation_id
              WHERE r.id = ? AND r.approved_by_admin = 1"; // Hanya menampilkan yang disetujui oleh admin
    $stmt = $pdo->prepare($query);
    $stmt->execute([$reservation_id]);
    $reservation = $stmt->fetch();

    if (!$reservation) {
        echo "Reservasi tidak ditemukan atau belum disetujui oleh admin.";
        exit();
    }

    // Format waktu mulai dan selesai
    $start_time = date('d-m-Y H:i', strtotime($reservation['start_time']));
    $end_time = date('d-m-Y H:i', strtotime($reservation['end_time']));
} else {
    echo "Reservation ID tidak ditemukan.";
    exit();
}

require_once 'includes/header.php';  // Menggunakan path relatif
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pembayaran Sewa Lapangan | RentSport</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .bukti-container {
            padding: 40px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 700px;
            margin: 0 auto;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="bukti-container">
    <h2 class="text-center mb-4">Bukti Pembayaran Sewa Lapangan</h2>

    <!-- Menampilkan detail reservasi yang sudah disetujui -->
    <div class="mb-3">
        <label class="form-label">Nama Lapangan</label>
        <input type="text" class="form-control" value="<?php echo ucfirst($reservation['field_name']); ?>" readonly>
    </div>

    <div class="mb-3">
        <label class="form-label">Waktu Mulai</label>
        <input type="text" class="form-control" value="<?php echo $start_time; ?>" readonly>
    </div>

    <div class="mb-3">
        <label class="form-label">Waktu Selesai</label>
        <input type="text" class="form-control" value="<?php echo $end_time; ?>" readonly>
    </div>

    <div class="mb-3">
        <label for="total_cost" class="form-label">Total Biaya</label>
        <input type="text" class="form-control" value="Rp <?php echo number_format($reservation['total_cost'], 0, ',', '.'); ?>" readonly>
    </div>

    <!-- Menampilkan status pembayaran -->
    <div class="mb-3">
        <label for="payment_status" class="form-label">Status Pembayaran</label>
        <input type="text" class="form-control" value="<?php echo ($reservation['payment_status'] == 'paid') ? 'Pembayaran Berhasil' : 'Menunggu Pembayaran'; ?>" readonly>
    </div>

    <!-- Tombol Kembali -->
    <div class="mb-3 text-center">
        <a href="dashboard.php" class="btn btn-primary w-100">Kembali ke Dashboard</a>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
