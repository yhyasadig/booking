<?php
class Notification {
    private $title;
    private $message;

    public function __construct($title, $message) {
        $this->title = $title;
        $this->message = $message;
    }

    // دالة لحفظ الإشعار في قاعدة البيانات
    public function saveToDatabase($connection) {
        try {
            $sql = "INSERT INTO notifications (title, message, created_at) VALUES (?, ?, NOW())";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param("ss", $this->title, $this->message);

            if ($stmt->execute()) {
                return "تم حفظ الإشعار بنجاح!";
            } else {
                throw new Exception("خطأ أثناء حفظ الإشعار: " . $stmt->error);
            }
        } catch (Exception $e) {
            return "خطأ: " . $e->getMessage();
        }
    }

    // دالة لاسترجاع جميع الإشعارات
    public static function fetchAll($connection) {
        try {
            $sql = "SELECT * FROM notifications ORDER BY created_at DESC";
            $result = $connection->query($sql);

            if ($result->num_rows > 0) {
                return $result->fetch_all(MYSQLI_ASSOC);
            } else {
                return [];
            }
        } catch (Exception $e) {
            throw new Exception("خطأ أثناء جلب الإشعارات: " . $e->getMessage());
        }
    }
}
?>
