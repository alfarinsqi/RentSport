<?php
// Include file untuk mengatur session dan koneksi ke database
require_once '../config/session.php';
require_once '../config/db.php';

// Cek apakah pengguna sudah login
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Ambil data lapangan yang tersedia
$query = "SELECT * FROM fields WHERE status = 'tersedia'";
$stmt = $pdo->prepare($query);
$stmt->execute();
$available_fields = $stmt->fetchAll();

// Ambil data lapangan yang terpesan
$query_reserved = "SELECT * FROM fields WHERE status = 'terpesan'";
$stmt_reserved = $pdo->prepare($query_reserved);
$stmt_reserved->execute();
$reserved_fields = $stmt_reserved->fetchAll();

require_once 'includes/header.php'; // Menggunakan path relatif
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengguna | RentSport</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 20px;
            padding-bottom: 40px;
        }

        .dashboard-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
            margin-bottom: 30px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        h2, h3 {
            color: #333;
            font-weight: 700;
            margin-bottom: 10px;
        }

        h3 {
            margin-bottom: 30px;
        }

        .card {
            border: none;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 3px;
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.2);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .card-body {
            padding: 25px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 10px; /* Menambahkan jarak yang konsisten antara elemen di dalam card */
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .card-text {
            color: #555;
            font-size: 1.1rem;
            margin-bottom: 10px; /* Mengurangi jarak bawah agar lebih dekat */
            flex-grow: 1;
        }

        .card-price {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2575fc;
            margin-top: 5px; /* Mengurangi jarak atas untuk mendekatkan dengan tipe lapangan */
        }

        .btn-reserve {
            background: linear-gradient(to right, #6a11cb, #2575fc);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            color: #fff;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            width: 100%;
            margin-top: auto;
        }

        .btn-reserve:hover {
            background: linear-gradient(to right, #2575fc, #6a11cb);
            transform: scale(1.02);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .section-header {
            text-align: left;
            margin-bottom: 20px;
        }

        .reserved-section {
            margin-top: 40px;
        }
    </style>
</head>
<body>

<div class="container mt-5">

    <div class="dashboard-container">
        <h3 class="section-header">Lapangan Tersedia</h3>
        <div class="row justify-content-start">
            <?php foreach ($available_fields as $field) : ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <?php
                        $gambar = strtolower($field['tipe']);
                        $gambarPath = "../assets/img/{$gambar}.jpg";
                        if (!file_exists($gambarPath)) {
                            $gambarPath = "../assets/img/{$gambar}.png";
                        }

                        if (!file_exists($gambarPath)) {
                            $gambarPath = "https://via.placeholder.com/400x200?text=Gambar+Tidak+Tersedia";
                        }
                        ?>
                        <img src="<?php echo $gambarPath; ?>" class="card-img-top" alt="<?php echo $field['nama']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $field['nama']; ?></h5>
                            <p class="card-text">Tipe: <?php echo ucfirst($field['tipe']); ?></p>
                            <p class="card-price">Harga: Rp <?php echo number_format($field['harga_per_jam'], 0, ',', '.'); ?> / Jam</p>
                            <a href="reservation.php?field_id=<?php echo $field['id']; ?>" class="btn btn-reserve">Pesan Sekarang</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($available_fields)): ?>
                <div class="col-12 text-center py-5">
                    <p class="lead text-muted">Belum ada lapangan yang tersedia saat ini.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Lapangan Terpesan -->
        <div class="reserved-section">
            <h3 class="section-header">Lapangan Terpesan</h3>
            <div class="row justify-content-start">
                <?php foreach ($reserved_fields as $field) : ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <?php
                            $gambar = strtolower($field['tipe']);
                            $gambarPath = "../assets/img/{$gambar}.jpg";
                            if (!file_exists($gambarPath)) {
                                $gambarPath = "../assets/img/{$gambar}.png";
                            }

                            if (!file_exists($gambarPath)) {
                                $gambarPath = "https://via.placeholder.com/400x200?text=Gambar+Tidak+Tersedia";
                            }
                            ?>
                            <img src="<?php echo $gambarPath; ?>" class="card-img-top" alt="<?php echo $field['nama']; ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $field['nama']; ?></h5>
                                <p class="card-text">Tipe: <?php echo ucfirst($field['tipe']); ?></p>
                                <span class="text-muted">Lapangan ini telah terpesan.</span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($reserved_fields)): ?>
                    <div class="col-12 text-center py-5">
                        <p class="lead text-muted">Tidak ada lapangan terpesan saat ini.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>
</html>
