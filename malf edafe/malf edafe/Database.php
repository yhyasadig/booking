<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'booking_system';
    private $username = 'root'; // استخدم اسم المستخدم المناسب
    private $password = ''; // استخدم كلمة المرور المناسبة
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            echo "خطأ في الاتصال بقاعدة البيانات: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>