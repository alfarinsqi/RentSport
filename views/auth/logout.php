<?php
// Terminate session and redirect to login page
require_once '../../config/session.php';
logout();

// Function to handle logout
function logout() {
    session_unset(); // Remove all session variables
    session_destroy(); // Destroy the session
    header("Location: login.php"); // Redirect to login page
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout | RentSport</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJt3W2O6FihRkH9r4tBvhF1Jt+Zt1RbG5U7qKzGZ2hF3Hs1HF2A5fTOUGOx7" crossorigin="anonymous">
    <style>
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc); /* Gradasi warna yang menarik */
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .logout-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .logout-container h2 {
            margin-bottom: 20px;
        }
        .btn-back {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="logout-container">
    <h2>Anda Telah Logout</h2>
    <p>Terima kasih telah menggunakan RentSport. Anda sekarang telah keluar dari akun Anda.</p>
    <a href="login.php" class="btn btn-primary btn-back">Kembali ke Halaman Login</a>
</div>

<!-- Link to Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
