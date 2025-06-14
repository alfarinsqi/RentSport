<?php
require_once '../config/session.php';
require_once '../config/db.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['reservation_id'])) {
    $reservation_id = $_GET['reservation_id'];

    // Ambil data reservasi
    $query = "SELECT * FROM reservations WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$reservation_id]);
    $reservation = $stmt->fetch();

    if (!$reservation) {
        echo "Reservasi tidak ditemukan.";
        exit();
    }

    $total_cost = isset($reservation['total_cost']) ? $reservation['total_cost'] : 0;

    // Ambil data lapangan
    $field_id = $reservation['field_id'];
    $query_field = "SELECT * FROM fields WHERE id = ?";
    $stmt_field = $pdo->prepare($query_field);
    $stmt_field->execute([$field_id]);
    $field = $stmt_field->fetch();

    if (!$field) {
        echo "Lapangan tidak ditemukan.";
        exit();
    }

    // Cek apakah waktu mulai dan selesai tersedia
    $formatted_start_time = isset($reservation['start_time']) && $reservation['start_time']
        ? date('d-m-Y H:i', strtotime($reservation['start_time']))
        : 'Belum ditentukan';

    $formatted_end_time = isset($reservation['end_time']) && $reservation['end_time']
        ? date('d-m-Y H:i', strtotime($reservation['end_time']))
        : 'Belum ditentukan';

} else {
    echo "Reservation ID tidak ditemukan.";
    exit();
}

require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran | RentSport</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #qris-container img {
            max-width: 350px;
            height: auto;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Pembayaran Reservasi Lapangan <?php echo ucfirst($field['tipe']); ?></h2>

    <!-- QRIS -->
    <div class="text-center" id="qris-container">
        <h4 class="mb-4">Silakan lakukan transfer menggunakan QRIS</h4>
        <img src="../assets/img/qris.png" class="img-fluid d-block mx-auto" alt="QRIS">
        <p class="text-center">Scan QR Code untuk melakukan pembayaran.</p>
    </div>

    <!-- Info Reservasi -->
    <div class="mb-3">
        <label class="form-label">Nama Lapangan</label>
        <input type="text" class="form-control" value="<?php echo $field['nama']; ?>" readonly>
    </div>

    <div class="mb-3">
        <label class="form-label">Waktu Mulai</label>
        <input type="text" class="form-control" value="<?php echo $formatted_start_time; ?>" readonly>
    </div>

    <div class="mb-3">
        <label class="form-label">Waktu Selesai</label>
        <input type="text" class="form-control" value="<?php echo $formatted_end_time; ?>" readonly>
    </div>

    <div class="mb-3">
        <label for="total_cost" class="form-label">Total Biaya</label>
        <input type="text" class="form-control" id="total_cost" value="Rp <?php echo number_format($total_cost, 0, ',', '.'); ?>" readonly>
    </div>

    <!-- Tombol Persetujuan -->
    <div class="mb-3">
        <a href="confirmation.php?reservation_id=<?php echo $reservation_id; ?>" class="btn btn-warning w-100">Tunggu Persetujuan Admin</a>
    </div>

    <div class="alert alert-info mt-4" role="alert">
        * Mohon melakukan transfer manual sesuai dengan jumlah total biaya yang tertera. Setelah transfer selesai, admin akan memverifikasi pembayaran Anda.
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>