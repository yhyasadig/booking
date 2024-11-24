<?php
require_once 'Database.php';

class PaymentMethods {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function addPaymentMethod($cardNumber, $methodName, $userID, $status = 'Pending') {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO PaymentMethods (cardNumber, paymentMethodName, paymentStatus, userID) 
                VALUES (:cardNumber, :methodName, :status, :userID)"
            );
            $stmt->bindParam(':cardNumber', $cardNumber);
            $stmt->bindParam(':methodName', $methodName);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':userID', $userID);
            $stmt->execute();
            return "تمت إضافة طريقة الدفع بنجاح.";
        } catch (PDOException $e) {
            return "خطأ أثناء إضافة طريقة الدفع: " . $e->getMessage();
        }
    }

    public function getPaymentMethods() {
        try {
            $stmt = $this->db->query("SELECT pm.*, u.userName AS userName 
                                      FROM PaymentMethods pm 
                                      JOIN Users u ON pm.userID = u.userID");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
