<?php
require_once '../config/db.php';

class ReviewModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Fungsi untuk membuat ulasan baru
    public function createReview($reservation_id, $rating, $comment) {
        $query = "INSERT INTO reviews (reservation_id, rating, comment) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([$reservation_id, $rating, $comment]);
    }

    // Fungsi untuk mendapatkan ulasan berdasarkan ID reservasi
    public function getReviewByReservationId($reservation_id) {
        $query = "SELECT * FROM reviews WHERE reservation_id = ?";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$reservation_id]);
        return $stmt->fetch();
    }

    // Fungsi untuk mendapatkan semua ulasan untuk lapangan tertentu
    public function getReviewsByFieldId($field_id) {
        $query = "SELECT * FROM reviews WHERE reservation_id IN (SELECT id FROM reservations WHERE field_id = ?)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([$field_id]);
        return $stmt->fetchAll();
    }
}
?>
