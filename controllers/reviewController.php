<?php
require_once '../config/db.php';
require_once '../config/session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $reservation_id = $_POST['reservation_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Simpan ulasan ke dalam database
    $query = "INSERT INTO reviews (reservation_id, rating, comment) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$reservation_id, $rating, $comment]);

    echo "Ulasan berhasil diberikan.";
}
?>
