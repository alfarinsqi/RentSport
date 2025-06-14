<?php
// Include file untuk session dan koneksi database
require_once '../config/session.php';
require_once '../config/db.php';

// Cek apakah admin sudah login
if (!isLoggedIn() || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Proses tambah lapangan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_field'])) {
    $nama = $_POST['nama'];
    $tipe = $_POST['tipe'];
    $harga_per_jam = $_POST['harga_per_jam'];
    $lokasi = $_POST['lokasi'];
    $status = $_POST['status'];

    if (!empty($nama) && !empty($tipe) && !empty($harga_per_jam) && !empty($lokasi) && !empty($status)) {
        $query = "INSERT INTO fields (nama, tipe, harga_per_jam, lokasi, status) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$nama, $tipe, $harga_per_jam, $lokasi, $status]);

        header("Location: admin_dashboard.php#manageFields");
        exit();
    }
}

// Proses hapus lapangan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_field']) && isset($_POST['delete_field_id'])) {
    $delete_field_id = $_POST['delete_field_id'];

    // Hapus hanya jika tidak ada reservasi aktif untuk lapangan tersebut
$check = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE field_id = ? AND status = 'confirmed'");
$check->execute([$delete_field_id]);
$count = $check->fetchColumn();

if ($count > 0) {
    echo "<script>alert('Gagal menghapus. Lapangan masih memiliki reservasi aktif.'); window.location.href='admin_dashboard.php';</script>";
} else {
    // Jika tidak ada reservasi yang aktif, lapangan bisa dihapus
    $query = "DELETE FROM fields WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$delete_field_id]);

    echo "<script>alert('Berhasil menghapus lapangan.'); window.location.href='admin_dashboard.php';</script>";
}

    header("Location: admin_dashboard.php#manageFields");
    exit();
} 

// Proses hapus lapangan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_field'])) {
    $delete_field_id = $_POST['delete_field_id'];

    try {
        $query = "DELETE FROM fields WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$delete_field_id]);

        header("Location: admin_dashboard.php");
        exit();
    } catch (PDOException $e) {
        echo "<script>alert('Gagal menghapus. Lapangan masih memiliki reservasi aktif.'); window.location.href='admin_dashboard.php';</script>";
    }
} 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_field'])) {
    $delete_field_id = $_POST['delete_field_id'];

    // Cek apakah field sedang digunakan di tabel reservations
    $check = $pdo->prepare("SELECT COUNT(*) FROM reservations WHERE field_id = ?");
    $check->execute([$delete_field_id]);
    $count = $check->fetchColumn();

    if ($count > 0) {
        echo "<script>alert('Gagal menghapus. Lapangan masih memiliki reservasi.'); window.location.href='admin_dashboard.php';</script>";
    } else {
        // Jika tidak ada reservasi terkait, baru hapus
        $query = "DELETE FROM fields WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$delete_field_id]);

        echo "<script>alert('Berhasil menghapus lapangan.'); window.location.href='admin_dashboard.php';</script>";
    }
}

// Proses hapus transaksi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_transaction']) && isset($_POST['delete_transaction_id'])) {
    $delete_transaction_id = $_POST['delete_transaction_id'];

    try {
        // Query untuk menghapus transaksi berdasarkan ID
        $query = "DELETE FROM transactions WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$delete_transaction_id]);

        // Redirect setelah menghapus transaksi
        header("Location: admin_dashboard.php#viewTransactions");
        exit();
    } catch (PDOException $e) {
        // Tangani jika terjadi error
        echo "<script>alert('Gagal menghapus transaksi.'); window.location.href='admin_dashboard.php#viewTransactions';</script>";
    }
}




// Ambil data lapangan
$query = "SELECT * FROM fields";
$stmt = $pdo->prepare($query);
$stmt->execute();
$fields = $stmt->fetchAll();

// Ambil data transaksi
$query_transactions = "SELECT * FROM transactions ORDER BY payment_date DESC";
$stmt_transactions = $pdo->prepare($query_transactions);
$stmt_transactions->execute();
$transactions = $stmt_transactions->fetchAll();

// Proses konfirmasi atau pembatalan reservasi
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['confirm'])) {
        $reservation_id = $_POST['reservation_id'];

        $query = "UPDATE reservations SET status = 'confirmed', payment_status = 'paid', approved_by_admin = 1 WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$reservation_id]);

        $query_field = "UPDATE fields SET status = 'terpesan' WHERE id = (SELECT field_id FROM reservations WHERE id = ?)";
        $stmt_field = $pdo->prepare($query_field);
        $stmt_field->execute([$reservation_id]);

        $query_res = "SELECT f.harga_per_jam, r.start_time, r.end_time FROM reservations r JOIN fields f ON r.field_id = f.id WHERE r.id = ?";
        $stmt_res = $pdo->prepare($query_res);
        $stmt_res->execute([$reservation_id]);
        $res_data = $stmt_res->fetch();

        if ($res_data) {
            $harga_per_jam = $res_data['harga_per_jam'];
            $start_time = new DateTime($res_data['start_time']);
            $end_time = new DateTime($res_data['end_time']);
            $jam_dipesan = $start_time->diff($end_time)->h;
            $total_pembayaran = $jam_dipesan * $harga_per_jam;

            $query_trans = "INSERT INTO transactions (reservation_id, amount, payment_status, payment_date) VALUES (?, ?, 'paid', NOW())";
            $stmt_trans = $pdo->prepare($query_trans);
            $stmt_trans->execute([$reservation_id, $total_pembayaran]);
        }

        header("Location: admin_dashboard.php#viewTransactions");
        exit();
    }

    if (isset($_POST['cancel'])) {
        $reservation_id = $_POST['reservation_id'];

        $query = "UPDATE reservations SET status = 'cancelled', approved_by_admin = 2 WHERE id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$reservation_id]);

        header('Location: admin_dashboard.php');
        exit();
    }
}

require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | RentSport</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Admin Dashboard</h2>
    <ul class="nav nav-tabs mt-4" id="adminTab" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="manageFieldsTab" data-bs-toggle="tab" href="#manageFields" role="tab">Kelola Lapangan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="manageReservationsTab" data-bs-toggle="tab" href="#manageReservations" role="tab">Kelola Reservasi</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="viewTransactionsTab" data-bs-toggle="tab" href="#viewTransactions" role="tab">Laporan Transaksi</a>
        </li>
    </ul>

    <div class="tab-content" id="adminTabContent">
        <!-- Kelola Lapangan -->
        <div class="tab-pane fade show active" id="manageFields" role="tabpanel">
            <h3 class="mt-4">Tambah Lapangan</h3>
            <form method="POST" action="admin_dashboard.php">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama Lapangan</label>
                    <input type="text" class="form-control" id="nama" name="nama" required>
                </div>
                <div class="mb-3">
                    <label for="tipe" class="form-label">Tipe Lapangan</label>
                    <select class="form-control" id="tipe" name="tipe" required>
                        <option value="futsal">Futsal</option>
                        <option value="badminton">Badminton</option>
                        <option value="tennis">Tennis</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="harga_per_jam" class="form-label">Harga per Jam</label>
                    <input type="number" class="form-control" id="harga_per_jam" name="harga_per_jam" required>
                </div>
                <div class="mb-3">
                    <label for="lokasi" class="form-label">Lokasi</label>
                    <input type="text" class="form-control" id="lokasi" name="lokasi" required>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-control" id="status" name="status" required>
                        <option value="tersedia">Tersedia</option>
                        <option value="terpesan">Terpesan</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100" name="add_field">Tambah Lapangan</button>
            </form>

            <hr>
            <h4 class="mt-4">Daftar Lapangan</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Tipe</th>
                        <th>Harga/Jam</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fields as $field): ?>
                        <tr>
                            <td><?= htmlspecialchars($field['nama']) ?></td>
                            <td><?= ucfirst($field['tipe']) ?></td>
                            <td>Rp <?= number_format($field['harga_per_jam'], 0, ',', '.') ?></td>
                            <td><?= htmlspecialchars($field['lokasi']) ?></td>
                            <td><?= ucfirst($field['status']) ?></td>
                            <td>
                                <form method="POST" action="admin_dashboard.php" onsubmit="return confirm('Yakin ingin menghapus lapangan ini?');">
                                    <input type="hidden" name="delete_field_id" value="<?= $field['id'] ?>">
                                    <button type="submit" name="delete_field" class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Kelola Reservasi -->
        <div class="tab-pane fade" id="manageReservations" role="tabpanel">
            <h3 class="mt-4">Reservasi Masuk</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Lapangan</th>
                        <th>Tanggal & Waktu</th>
                        <th>Status Pembayaran</th>
                        <th>Status Reservasi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query_reservations = "SELECT * FROM reservations WHERE status = 'pending' AND approved_by_admin = 0";
                    $stmt_reservations = $pdo->prepare($query_reservations);
                    $stmt_reservations->execute();
                    $reservations = $stmt_reservations->fetchAll();

                    foreach ($reservations as $reservation):
                        $field_id = $reservation['field_id'];
                        $stmt_field = $pdo->prepare("SELECT * FROM fields WHERE id = ?");
                        $stmt_field->execute([$field_id]);
                        $field = $stmt_field->fetch();
                    ?>
                        <tr>
                            <td><?= $reservation['id'] ?></td>
                            <td><?= $field['nama'] ?></td>
                            <td><?= $reservation['start_time'] ?> - <?= $reservation['end_time'] ?></td>
                            <td><?= ucfirst($reservation['payment_status']) ?></td>
                            <td><?= ucfirst($reservation['status']) ?></td>
                            <td>
                                <form method="POST" action="admin_dashboard.php">
                                    <input type="hidden" name="reservation_id" value="<?= $reservation['id'] ?>">
                                    <button type="submit" class="btn btn-success" name="confirm">Setujui Pembayaran</button>
                                    <button type="submit" class="btn btn-danger" name="cancel">Tolak Pembayaran</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

<!-- Laporan Transaksi -->
            <div class="tab-pane fade" id="viewTransactions" role="tabpanel">
                <h3 class="mt-4">Laporan Transaksi</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID Reservasi</th>
                            <th>Jumlah Pembayaran</th>
                            <th>Status Pembayaran</th>
                            <th>Tanggal Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $transaction): ?>
                            <tr>
                                <td><?= $transaction['id'] ?></td>
                                <td><?= $transaction['reservation_id'] ?></td>
                                <td>Rp <?= number_format($transaction['amount'], 0, ',', '.') ?></td>
                                <td><?= ucfirst($transaction['payment_status']) ?></td>
                                <td><?= $transaction['payment_date'] ?></td>
                                <td>
                                    <!-- Tombol untuk menghapus transaksi -->
                                    <form method="POST" action="admin_dashboard.php" onsubmit="return confirm('Yakin ingin menghapus transaksi ini?');">
                                        <input type="hidden" name="delete_transaction_id" value="<?= $transaction['id'] ?>">
                                        <button type="submit" name="delete_transaction" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>