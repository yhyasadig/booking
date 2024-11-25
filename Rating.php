<?php
class Rating {
    private $db;

    public function __construct($conn) {
        $this->db = $conn;
    }

    public function insertRating($userID, $eventID, $rating, $review) {
        $query = "INSERT INTO ratings (userID, eventID, rating, review) VALUES (:userID, :eventID, :rating, :review)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':userID', $userID);
        $stmt->bindParam(':eventID', $eventID);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':review', $review);
        return $stmt->execute();
    }
}
?>
