<?php
class Notification {
    private $title;
    private $message;
    private $isGlobal;

    // المُنشئ
    public function __construct($title, $message, $isGlobal = 0) {
        $this->title = $title;
        $this->message = $message;
        $this->isGlobal = $isGlobal;
    }

    // دالة لحفظ الإشعار في قاعدة البيانات
    public function save($conn) {
        try {
            $sql = "INSERT INTO notifications (title, message, is_global, created_at) VALUES (?, ?, ?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$this->title, $this->message, $this->isGlobal]);
            return "تمت إضافة الإشعار بنجاح.";
        } catch (PDOException $e) {
            throw new Exception("خطأ أثناء حفظ الإشعار: " . $e->getMessage());
        }
    }

    // دالة لاسترجاع جميع الإشعارات
    public static function fetchAll($conn, $isGlobal = null) {
        try {
            $sql = "SELECT title, message, created_at FROM notifications";
            if ($isGlobal !== null) {
                $sql .= " WHERE is_global = ?";
                $stmt = $conn->prepare($sql);
                $stmt->execute([$isGlobal]);
            } else {
                $stmt = $conn->prepare($sql);
                $stmt->execute();
            }
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            throw new Exception("خطأ أثناء جلب الإشعارات: " . $e->getMessage());
        }
    }
}
?>
