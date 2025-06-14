<?php
require_once '../config/session.php';
require_once '../config/db.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['field_id'])) {
    $field_id = $_GET['field_id'];
    $query = "SELECT * FROM fields WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$field_id]);
    $field = $stmt->fetch();
} else {
    header('Location: dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reserve'])) {
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $start_timestamp = strtotime($start_time);
    $end_timestamp = strtotime($end_time);

    if ($start_timestamp === false || $end_timestamp === false) {
        echo "Waktu yang dimasukkan tidak valid.";
        exit();
    }

    $duration = ($end_timestamp - $start_timestamp) / 3600;

    if ($duration <= 0) {
        echo "Durasi sewa lapangan harus lebih dari 0 jam.";
        exit();
    }

    $total_cost = $field['harga_per_jam'] * $duration;
    $user_id = $_SESSION['user_id'];

    $pdo->beginTransaction();

    try {
        $query = "INSERT INTO reservations (user_id, field_id, start_time, end_time, total_cost, status, payment_status) 
                  VALUES (?, ?, ?, ?, ?, 'pending', 'pending')";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$user_id, $field_id, $start_time, $end_time, $total_cost]);

        $reservation_id = $pdo->lastInsertId();
        $pdo->commit();

        header("Location: payment.php?reservation_id=$reservation_id");
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}

require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservasi Lapangan | RentSport</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center">Reservasi Lapangan <?php echo ucfirst($field['tipe']); ?></h2>

    <form method="POST" action="reservation.php?field_id=<?php echo $field['id']; ?>">
        <div class="mb-3">
            <label for="start_time" class="form-label">Waktu Mulai</label>
            <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
        </div>
        <div class="mb-3">
            <label for="end_time" class="form-label">Waktu Selesai</label>
            <input type="datetime-local" class="form-control" id="end_time" name="end_time" required>
        </div>

        <div class="mb-3">
            <label for="total_cost" class="form-label">Total Biaya</label>
            <input type="text" class="form-control" id="total_cost" name="total_cost" value="Rp 0" readonly>
        </div>

        <!-- Simpan harga per jam sebagai data-atribut -->
        <input type="hidden" id="harga_per_jam" value="<?php echo $field['harga_per_jam']; ?>">

        <button type="submit" class="btn btn-primary w-100" name="reserve">Pesan Sekarang</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const startInput = document.getElementById('start_time');
    const endInput = document.getElementById('end_time');
    const totalCostInput = document.getElementById('total_cost');
    const hargaPerJam = parseFloat(document.getElementById('harga_per_jam').value);

    function updateTotalCost() {
        const startTime = new Date(startInput.value);
        const endTime = new Date(endInput.value);

        if (startInput.value && endInput.value && endTime > startTime) {
            const duration = (endTime - startTime) / (1000 * 60 * 60); // dalam jam
            const totalCost = duration * hargaPerJam;

            // Format rupiah
            const formatter = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            });

            totalCostInput.value = formatter.format(totalCost);
        } else {
            totalCostInput.value = 'Rp 0';
        }
    }

    startInput.addEventListener('change', updateTotalCost);
    endInput.addEventListener('change', updateTotalCost);
</script>

</body>
</html>