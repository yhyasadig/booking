<?php

class User {
    private $conn;
    private $table_name = "users";

    public $userid;
    public $username;
    public $email;
    public $password;
    public $profession;  // المهنة المضافة للمستخدم
    public $discountID;  // معرف الخصم المرتبط بالمهنة

    public function __construct($db) {
        $this->conn = $db;
    }

    // تسجيل المستخدم الجديد في قاعدة البيانات
    public function register() {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                      (username, email, password, profession, discountID) 
                      VALUES (:username, :email, :password, :profession, :discountID)";
            $stmt = $this->conn->prepare($query);

            // تشفير كلمة المرور
            $hashed_password = password_hash($this->password, PASSWORD_BCRYPT);

            // ربط البيانات بالاستعلام
            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':profession', $this->profession);
            $stmt->bindParam(':discountID', $this->discountID);

            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $exception) {
            echo "Registration Error: " . $exception->getMessage();
            return false;
        }
    }

    // تسجيل دخول المستخدم
    public function login() {
        try {
            $query = "SELECT userid, password, discountID FROM " . $this->table_name . " WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $this->email);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($this->password, $row['password'])) {
                    $this->userid = $row['userid'];
                    $this->discountID = $row['discountID'];
                    return true;
                }
            }
            return false;
        } catch (PDOException $exception) {
            echo "Login Error: " . $exception->getMessage();
            return false;
        }
    }

    // الحصول على معرف المستخدم بناءً على البريد الإلكتروني
    public function getUseridByEmail() {
        try {
            $query = "SELECT userid FROM " . $this->table_name . " WHERE email = :email LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $this->email);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['userid'] ?? null;
        } catch (PDOException $exception) {
            echo "Fetch User ID Error: " . $exception->getMessage();
            return null;
        }
    }

    // الحصول على معرف الخصم بناءً على المهنة
    public function fetchDiscountIDByProfession() {
        try {
            $query = "SELECT DiscountID FROM Discounts WHERE Profession = :profession LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':profession', $this->profession);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['DiscountID'] ?? null;
        } catch (PDOException $exception) {
            echo "Fetch Discount ID Error: " . $exception->getMessage();
            return null;
        }
    }
}
?>
