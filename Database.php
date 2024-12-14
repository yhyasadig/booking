<?php
class Database {
    private $host = 'localhost'; // عنوان الخادم
    private $db_name = 'booking_system2'; // اسم قاعدة البيانات
    private $username = 'root'; // اسم المستخدم
    private $password = ''; // كلمة المرور
    public $conn; // اتصال قاعدة البيانات

    // دالة للحصول على الاتصال بقاعدة البيانات
    public function getConnection() {
        $this->conn = null; // تهيئة الاتصال كقيمة فارغة
        try {
            // إنشاء الاتصال باستخدام PDO
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name}",
                $this->username,
                $this->password
            );
            // إعداد خيارات الاتصال
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // وضع الإبلاغ عن الأخطاء
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // وضع جلب البيانات كمصفوفة
        } catch (PDOException $exception) {
            // عرض رسالة خطأ واضحة
            echo "⚠️ حدث خطأ أثناء الاتصال بقاعدة البيانات: " . $exception->getMessage();
        }
        return $this->conn;
    }

    // دالة لإغلاق الاتصال بقاعدة البيانات
    public function closeConnection() {
        $this->conn = null; // تعيين الاتصال إلى null لتحرير الموارد
    }
}
?>
