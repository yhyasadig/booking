<?php
class Discount {
    private $conn;
    private $table_name = "discounts";

    public $discountID;
    public $profession;
    public $discountAmount;
    public $description;

    public function __construct($db) {
        $this->conn = $db;
    }

    // إضافة خصم جديد
    public function addDiscount() {
        try {
            $query = "INSERT INTO {$this->table_name} (Profession, DiscountAmount, Description)
                      VALUES (:profession, :discountAmount, :description)";
            $stmt = $this->conn->prepare($query);

            // ربط القيم
            $stmt->bindParam(':profession', $this->profession);
            $stmt->bindParam(':discountAmount', $this->discountAmount);
            $stmt->bindParam(':description', $this->description);

            return $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("خطأ أثناء إضافة الخصم: " . $e->getMessage());
        }
    }

    // جلب جميع الخصومات
    public function getAllDiscounts() {
        try {
            $query = "SELECT * FROM {$this->table_name}";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("خطأ أثناء جلب الخصومات: " . $e->getMessage());
        }
    }
}
?>
