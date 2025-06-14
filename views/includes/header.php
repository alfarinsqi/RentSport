<!-- Menyertakan file CSS khusus header dan footer -->
<link rel="stylesheet" href="../assets/css/hf.css">

<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(to right, #6A11CB, #2575FC);">
    <div class="container-fluid">
        <a class="navbar-brand" href="dashboard.php">RentSport</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (!isLoggedIn()) : ?>
                    <!-- Jika belum login, tampilkan menu Login dan Daftar -->
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Daftar</a>
                    </li>
                <?php else : ?>
                    <!-- Jika sudah login, tampilkan menu sesuai dengan role -->
                    <?php if ($_SESSION['role'] === 'admin') : ?>
                        <!-- Jika Admin yang login -->
                        <li class="nav-item">
                            <a class="nav-link" href="admin_dashboard.php">Admin Dashboard</a>
                        </li>
                        <!-- Tambahkan menu untuk Admin -->
                        <li class="nav-item">
                            <a class="nav-link" href="delete_reservation.php">Kelola Pemesanan</a>
                        </li>
                    <?php endif; ?>
                    <!-- User yang login -->
                    <li class="nav-item">
                        <a class="nav-link" href="confirmation.php">Riwayat Pemesanan</a>
                    </li>

                    <!-- Cek Status Pembayaran hanya untuk User dengan pending reservation -->
                    <?php 
                    if ($_SESSION['role'] !== 'admin') {
                        // Cek apakah user memiliki reservasi yang statusnya pending
                        $user_id = $_SESSION['user_id'];
                        $query = "SELECT id FROM reservations WHERE user_id = ? AND status = 'pending' ORDER BY id DESC LIMIT 1"; // Ambil ID terbesar
                        $stmt = $pdo->prepare($query);
                        $stmt->execute([$user_id]);
                        $reservation = $stmt->fetch();

                        // Jika ada reservasi pending, tampilkan tombol cek status pembayaran
                        if ($reservation) {
                            $reservation_id = $reservation['id']; // Ambil reservation_id dari hasil query
                            echo '<li class="nav-item">
                                    <a class="nav-link" href="waiting_approval.php?reservation_id=' . $reservation_id . '">Cek Status Pembayaran</a>
                                  </li>';
                        } else {
                            echo '<li class="nav-item">
                                    <a class="nav-link" href="waiting_approval.php?reservation_id=1">Status Pembayaran</a>
                                  </li>';
                        }
                        
                    }
                    ?>
                    
                    <!-- Menu logout -->
                    <li class="nav-item">
                        <a class="nav-link" href="../views/auth/login.php?logout=true">Logout</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
