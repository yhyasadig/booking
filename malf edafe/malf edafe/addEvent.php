<?php
require_once 'Database.php';
require_once 'User.php';

session_start(); // بدء الجلسة

// تحقق من أن المستخدم قد قام بتسجيل الدخول
if (!isset($_SESSION['userID'])) {
    echo "<script>alert('يرجى تسجيل الدخول أولاً.'); window.location.href='login.php';</script>";
    exit;
}

class Events {
    private $db;

    public function __construct() {
        try {
            $this->db = new PDO("mysql:host=localhost;dbname=booking_system", "root", "");
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
        }
    }

    public function addEvent($name, $date, $location, $type, $imagePath) {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO Events (eventName, eventDate, eventLocation, eventType, eventImage) 
                 VALUES (:name, :date, :location, :type, :image)"
            );
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':image', $imagePath);
            $stmt->execute();

            // إضافة الصورة إلى الصفحة المناسبة
            $this->appendImageToFile($type, $imagePath);

            return "تمت إضافة الحدث بنجاح.";
        } catch (PDOException $e) {
            return "خطأ أثناء إضافة الحدث: " . $e->getMessage();
        }
    }

    private function appendImageToFile($type, $imagePath) {
        $filesMap = [
            'عرض طيران' => '/mnt/data/Flight_Event_Offerings.php',
            'عرض مهرجان' => '/mnt/data/National_Festival_Offerings.php',
            'عرض كرة قدم' => '/mnt/data/LaLigaMatchOfferings.php',
            'عرض فيلم' => '/mnt/data/Cinema_Event_Offerings.php',
        ];

        if (isset($filesMap[$type])) {
            $filePath = $filesMap[$type];
            $imageTag = '<div class="event-box">
                            <img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($type) . '">
                         </div>';
            file_put_contents($filePath, $imageTag, FILE_APPEND); // إضافة الصورة إلى نهاية الملف
        }
    }
}

$events = new Events();

// معالجة الإضافة إذا تم إرسال النموذج
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['eventName'];
    $date = $_POST['eventDate'];
    $location = $_POST['eventLocation'];
    $type = $_POST['eventType'];

    // حفظ الصورة في مجلد معين
    $imageDirectory = 'uploads/';
    $imagePath = $imageDirectory . basename($_FILES['eventImage']['name']);
    move_uploaded_file($_FILES['eventImage']['tmp_name'], $imagePath);

    $message = $events->addEvent($name, $date, $location, $type, $imagePath);

    // إعادة توجيه لتجنب تكرار الإدخال
    header('Location: ' . $_SERVER['PHP_SELF'] . '?success=1');
    exit;
}

// التحقق من وجود رسالة نجاح
if (isset($_GET['success']) && $_GET['success'] == 1) {
    $message = "تمت إضافة الحدث بنجاح.";
}
?>
