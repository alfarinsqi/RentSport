<?php
require_once '../config/session.php';
require_once '../config/db.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Proses hapus reservasi
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Pastikan ID valid
    if (is_numeric($delete_id)) {
        // Query untuk menghapus reservasi berdasarkan ID
        $query = "DELETE FROM reservations WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$delete_id]);

        // Setelah berhasil menghapus, alihkan kembali ke halaman daftar reservasi
        header('Location: delete_reservation.php');
        exit();
    }
}

// Query untuk mendapatkan semua reservasi
$query = "SELECT r.id, r.start_time, r.end_time, r.total_cost, f.nama AS field_name, r.payment_status, r.approved_by_admin
          FROM reservations r
          LEFT JOIN fields f ON r.field_id = f.id";
$stmt = $pdo->prepare($query);
$stmt->execute();
$reservations = $stmt->fetchAll();

require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Reservasi | RentSport</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Daftar Reservasi yang Diajukan</h2>

    <!-- Tabel daftar reservasi -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID Reservasi</th>
                <th>Lapangan</th>
                <th>Waktu Mulai</th>
                <th>Waktu Selesai</th>
                <th>Total Biaya</th>
                <th>Status Pembayaran</th>
                <th>Status Persetujuan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $reservation) : ?>
                <tr>
                    <td><?php echo $reservation['id']; ?></td>
                    <td><?php echo ucfirst($reservation['field_name']); ?></td>
                    <td><?php echo date('d-m-Y H:i', strtotime($reservation['start_time'])); ?></td>
                    <td><?php echo date('d-m-Y H:i', strtotime($reservation['end_time'])); ?></td>
                    <td>Rp <?php echo number_format($reservation['total_cost'], 0, ',', '.'); ?></td>
                    <td>
                        <?php
                        // Menampilkan status pembayaran
                        if ($reservation['payment_status'] == 'paid') {
                            echo "Pembayaran Selesai";
                        } else {
                            echo "Menunggu Pembayaran";
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        // Menampilkan status persetujuan admin
                        if ($reservation['approved_by_admin'] == 1) {
                            echo "<span class='text-success'>Disetujui</span>";
                        } elseif ($reservation['approved_by_admin'] == 2) {
                            echo "<span class='text-danger'>Ditolak</span>";
                        } else {
                            echo "<span class='text-warning'>Menunggu Persetujuan</span>";
                        }
                        ?>
                    </td>
                    <td>
                        <a href="delete_reservation.php?delete_id=<?php echo $reservation['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus reservasi ini?');">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
require_once 'includes/footer.php';
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
