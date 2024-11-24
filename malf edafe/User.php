<?php
class User {
    private $conn;
    private $table_name = "users";

    public $userid; // معرف المستخدم
    public $username;
    public $email;
    public $password;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register() {
        try {
            $query = "INSERT INTO " . $this->table_name . " (username, email, password) VALUES (:username, :email, :password)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':password', password_hash($this->password, PASSWORD_BCRYPT));
            if ($stmt->execute()) {
                return true;
            }
            return false;
        } catch (PDOException $exception) {
            echo "خطأ في التسجيل: " . $exception->getMessage();
            return false;
        }
    }

    public function login() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE email = :email";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $this->email);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if (password_verify($this->password, $row['password'])) {
                    $this->userid = $row['userid']; // تعيين معرف المستخدم بعد التحقق
                    return true;
                }
            }
            return false;
        } catch (PDOException $exception) {
            echo "خطأ في تسجيل الدخول: " . $exception->getMessage();
            return false;
        }
    }

    // إضافة دالة لاسترجاع معرف المستخدم
    public function getUserID() {
        return $this->userid;
    }

    public function updateProfile() {
        // يمكنك إضافة منطق تعديل بيانات المستخدم هنا
    }
}
?>
